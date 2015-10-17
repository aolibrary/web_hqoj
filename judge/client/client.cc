#include <sys/types.h>
#include <sys/wait.h>
#include <sys/stat.h>
#include <sys/resource.h>
#include <sys/ptrace.h>
#include <sys/user.h>
#include <stdarg.h>
#include <time.h>
#include <fcntl.h>
#include <unistd.h>
#include <dirent.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <mysql/mysql.h>
#include "enabled_syscall.h"
#include <string>
#include <vector>
#include <algorithm>
using namespace std;

#define QUEUE              0
#define REJUDGE            1
#define COMPILING          2
#define RUNNING            3
#define ACCEPTED           4
#define PRESENTATION_ERROR 5
#define WRONG_ANSWER       6
#define TIME_EXCEEDED      7
#define MEMORY_EXCEEDED    8
#define OUTPUT_EXCEEDED    9
#define RUNTIME_ERROR      10
#define COMPILATION_ERROR  11
#define TIME_OUT           12
#define INVALID            13

static char resultFmt[14][50] = {
    "Queue",
    "Queue（Rejudge）",
    "Compiling",
    "Running",
    "Accepted",
    "Presentation Error",
    "Wrong Answer",
    "Time Limit Exceeded",
    "Memory Limit Exceeded",
    "Output Limit Exceeded",
    "Runtime Error",
    "Compilation Error",
    "Time Out",
    "Invalid"
};

#define CC    3
#define CPLUS 4

#define BUF_SIZE 512
#define STD_KB 1024
#define STD_MB 1048576
#define STD_F_LIM (STD_MB<<5)       // 32M
#define MAX_TIME_LIMIT 10000        // 10s
#define MAX_MEMORY_LIMIT 256*1024   // 256M
#define MAX_AS_LIMIT 600            // set AS 600M

#ifdef __i386
#define REG_SYSCALL orig_eax
#define REG_RET eax
#define REG_ARG0 ebx
#define REG_ARG1 ecx
#define REG_ARG4 edi
#else
#define REG_SYSCALL orig_rax
#define REG_RET rax
#define REG_ARG0 rdi
#define REG_ARG1 rsi
#define REG_ARG4 r8
#endif

static char ojHome[BUF_SIZE] = "/home/judge";
static char workDir[BUF_SIZE];
static char langExt[7][8] = { "", "c", "cc", "c", "cc" };

// mysql conf
// static char hostname[BUF_SIZE] = "192.168.56.102";
// static char username[BUF_SIZE] = "root";
// static char password[BUF_SIZE] = "123";
static char hostname[BUF_SIZE] = "hqoj0513.mysql.rds.aliyuncs.com";
static char username[BUF_SIZE] = "hqoj";
static char password[BUF_SIZE] = "okpl3981";
static char dbname[BUF_SIZE]   = "hqoj";
static int  port               = 3306;
MYSQL *conn;

static int clientId;

struct SolutionInfo {
    int queueId;
    char problemId[BUF_SIZE];
    int language;
    int timeLimit;
    int memoryLimit;
    int solutionId;
    // result
    int timeCost;
    int memoryCost;
    int result;
    int judgeTime;
};
SolutionInfo solutionInfo;

void writeLog(const char *fmt, ...) {

    char buffer[4096];
    sprintf(buffer, "%s/log/client.log", ojHome);
    FILE *fp = fopen(buffer, "a+");
    if (fp == NULL) {
        fprintf(stderr, "openfile error!\n");
        system("pwd");
    }

    time_t timep;
    struct tm *p;
    time(&timep);
    p = localtime(&timep);
    char timeStr[50];
    sprintf(timeStr, "%4d-%02d-%02d %02d:%02d:%02d"
        , 1900+p->tm_year, 1+p->tm_mon, p->tm_mday
        , p->tm_hour, p->tm_min, p->tm_sec);

    va_list ap;
    va_start(ap, fmt);
    vsprintf(buffer, fmt, ap);
    fprintf(fp, "%s qid:%d %s\n", timeStr, solutionInfo.queueId, buffer);
    va_end(ap);
    fclose(fp);
}

int initMysql() {

    conn = mysql_init(NULL);
    const char timeout = 30;
    mysql_options(conn, MYSQL_OPT_CONNECT_TIMEOUT, &timeout);
    if (!mysql_real_connect(conn, hostname, username, password, dbname, port, 0, 0)) {
        writeLog("%s", mysql_error(conn));
        return 0;
    }
    const char * utf8sql = "set names utf8mb4";
    if (mysql_real_query(conn, utf8sql, strlen(utf8sql))) {
        writeLog("%s", mysql_error(conn));
        return 0;
    }
    return 1;
}

int getSolutionInfo() {

    MYSQL_RES *res;
    MYSQL_ROW row;
    char sql[BUF_SIZE];
    sprintf(sql
        , "SELECT problem_id, language, time_limit, memory_limit, source, solution_id FROM oj_judge where id=%d"
        , solutionInfo.queueId);
    if(mysql_real_query(conn, sql, strlen(sql)) != 0) {
        writeLog("%s", mysql_error(conn));
        return 0;
    }
    res = mysql_store_result(conn);
    row = mysql_fetch_row(res);
    mysql_free_result(res);
    if (!row) {
        exit(0);
    }
    strcpy(solutionInfo.problemId, row[0]);
    solutionInfo.language    = atoi(row[1]);
    solutionInfo.timeLimit   = atoi(row[2]);
    solutionInfo.memoryLimit = atoi(row[3]);
    solutionInfo.solutionId  = atoi(row[5]);

    // Save Source
    char srcPath[BUF_SIZE];
    sprintf(srcPath, "%s/Main.%s", workDir, langExt[solutionInfo.language]);
    FILE *fp = fopen(srcPath, "w");
    fprintf(fp, "%s", row[4]);
    fclose(fp);
    return 1;
}

int execute_cmd(const char * fmt, ...) {
    char cmd[BUF_SIZE];
    int ret = 0;
    va_list ap;
    va_start(ap, fmt);
    vsprintf(cmd, fmt, ap);
    ret = system(cmd);
    va_end(ap);
    return ret;
}

int compile() {

    const char * CP_CC[]= { "gcc", "Main.c", "-o", "Main", "-fno-asm", "-Wall",
            "-lm", "--static", "-std=c99", "-DONLINE_JUDGE", NULL };
    const char * CP_CPLUS[]= { "g++", "Main.cc", "-o", "Main", "-fno-asm", "-Wall",
            "-lm", "--static", "-DONLINE_JUDGE", NULL };

    int lang = solutionInfo.language;
    pid_t pid = fork();
    if (pid == 0) {
        struct rlimit LIM;
        LIM.rlim_max = 60;
        LIM.rlim_cur = 60;
        setrlimit(RLIMIT_CPU, &LIM);
        alarm(60);
        LIM.rlim_max = 100 * STD_MB;
        LIM.rlim_cur = 100 * STD_MB;
        setrlimit(RLIMIT_FSIZE, &LIM);
        LIM.rlim_max = STD_MB << 10;
        LIM.rlim_cur = STD_MB << 10;
        setrlimit(RLIMIT_AS, &LIM);

        freopen("ce.txt", "w", stderr);

        execute_cmd("chown judge *");
        while(setgid(1536)!=0) sleep(1);
        while(setuid(1536)!=0) sleep(1);
        while(setresuid(1536, 1536, 1536)!=0) sleep(1);

        switch (lang) {
            case CC:
                execvp(CP_CC[0], (char * const *) CP_CC);
                break;
            case CPLUS:
                execvp(CP_CPLUS[0], (char * const *) CP_CPLUS);
                break;
            default:
                exit(1);
        }
        exit(0);
    } else {
        int status = 0;
        waitpid(pid, &status, 0);
        return status;
    }
}

int isInFile(const char fname[]) {

    int l = strlen(fname);
    if (l <= 3 || strcmp(fname + l - 3, ".in") != 0) {
        return 0;
    }
    return l - 3;
}

long getFilesize(const char * filename) {
    struct stat f_stat;
    if (stat(filename, &f_stat) == -1) {
        return 0;
    }
    return (long) f_stat.st_size;
}

bool prepareFiles(const char* casename, char* inFile, char* outFile, char* userFile) {

    sprintf(inFile, "%s/data/%s/%s.in", ojHome, solutionInfo.problemId, casename);
    sprintf(outFile, "%s/data/%s/%s.out", ojHome, solutionInfo.problemId, casename);
    sprintf(userFile, "%s/user.out", workDir);

    if (access(inFile, F_OK) == -1) {
        return false;
    } else {
        execute_cmd("/bin/cp %s %s/data.in", inFile, workDir);
        return true;
    }
}

void cleanWorkDir() {

    if (0 == strlen(workDir)) {
        exit(1);
    }
    execute_cmd("/bin/rm -Rf %s/*", workDir);
}

void runSolution() {

    nice(19);
    chdir(workDir);

    freopen("data.in", "r", stdin);
    freopen("user.out", "w", stdout);
    freopen("re.txt", "a+", stderr);

    ptrace(PTRACE_TRACEME, 0, NULL, NULL);

    while(setgid(1536)!=0) sleep(1);
    while(setuid(1536)!=0) sleep(1);
    while(setresuid(1536, 1536, 1536)!=0) sleep(1);

    // time limit
    struct rlimit LIM;
    LIM.rlim_cur = solutionInfo.timeLimit/1000;
    LIM.rlim_max = LIM.rlim_cur;
    setrlimit(RLIMIT_CPU, &LIM);
    alarm(0);
    alarm(solutionInfo.timeLimit/100);

    // file limit
    LIM.rlim_max = STD_F_LIM + STD_MB;
    LIM.rlim_cur = STD_F_LIM;
    setrlimit(RLIMIT_FSIZE, &LIM);

    // proc limit
    LIM.rlim_cur = LIM.rlim_max = 1;
    setrlimit(RLIMIT_NPROC, &LIM);

    // set the stack
    LIM.rlim_cur = STD_MB << 6;
    LIM.rlim_max = STD_MB << 6;
    setrlimit(RLIMIT_STACK, &LIM);

    // set the memory
    LIM.rlim_cur = MAX_AS_LIMIT*STD_MB;
    LIM.rlim_max = LIM.rlim_cur;
    setrlimit(RLIMIT_AS, &LIM);

    switch (solutionInfo.language) {
        case CC:
        case CPLUS:
            execl("./Main", "./Main", (char *) NULL);
            break;
    }

    exit(0);
}

int getProcStatus(int pid, const char* mark) {

    FILE *pf;
    char fn[BUF_SIZE], buf[BUF_SIZE];
    int ret = 0;
    sprintf(fn, "/proc/%d/status", pid);
    pf = fopen(fn, "r");
    int m = strlen(mark);
    while (pf && fgets(buf, BUF_SIZE - 1, pf)) {
        buf[strlen(buf) - 1] = 0;
        if (strncmp(buf, mark, m) == 0) {
            sscanf(buf + m + 1, "%d", &ret);
        }
    }
    if (pf) {
        fclose(pf);
    }
    return ret;
}

void logRuntimeError(char* err) {

    FILE *ferr = fopen("re.txt", "a+");
    fprintf(ferr, "Runtime Error: %s\n", err);
    fclose(ferr);
}

void logDetail(const char* info) {

    FILE *fh = fopen("detail.txt", "a+");
    fprintf(fh, "%s\n", info);
    fclose(fh);
}

// part of syscall
int readStringFromTracedProcess(pid_t pid, unsigned long address, char* buffer, int max_length) {

    for (int i = 0; i < max_length; i += sizeof(long)) {
        long data = ptrace(PTRACE_PEEKDATA, pid, address + i, 0);
        if (data == -1) {
            writeLog("Fail to read address %d", address + i);
            return -1;
        }
        char* p = (char*) &data;
        for (int j = 0; j < (int) sizeof(long); j++, p++) {
            if (*p && i + j < max_length) {
                if (isprint(*p)) {
                    buffer[i + j] = *p;
                } else {
                    writeLog("Unrecoginized character 0x %x", (int)(*p));
                    return -1;
                }
            } else {
                buffer[i + j] = 0;
                return 0;
            }
        }
    }
    buffer[max_length] = 0;
    return 0;
}

bool allowedFileAccess(const char* path) {

    char path_buffer[BUF_SIZE+1];
    realpath(path, path_buffer);

    if (strncmp(path_buffer, ojHome, strlen(ojHome)) == 0) {
        writeLog("Accessing %s is not allowed", path_buffer);
        return false;
    }
    return true;
}

bool handleSyscall(pid_t pid, struct user_regs_struct& regs, int &first_execve, int before_syscall, int &caseResult, unsigned long &requested_brk) {

    char path[BUF_SIZE];

    switch(regs.REG_SYSCALL) {
        case SYS_exit:
        case SYS_exit_group:
            break;
        case SYS_execve:
            if (first_execve) {
                first_execve = 0;
                ptrace(PTRACE_SYSCALL, pid, 0, 0);
                return true;
            }
            break;
        case SYS_brk:
            if (before_syscall) {
                requested_brk = regs.REG_ARG0;
            } else {
                if (regs.REG_RET < requested_brk) {
                    caseResult = MEMORY_EXCEEDED;
                    ptrace(PTRACE_KILL, pid, 0, 0);
                    return true;
                }
            }
    #if __WORDSIZE == 64
        case SYS_select:
    #else
        case SYS__newselect:
    #endif
            if (before_syscall) {
                long address = regs.REG_ARG4;
                if (address == 0) {
                    break;
                }
                size_t i;
                memset(path, 0, sizeof(struct timeval));
                for (i = 0; i < sizeof(struct timeval); i += sizeof(long)) {
                    long data = ptrace(PTRACE_PEEKDATA, pid, address + i, 0);
                    long* buf = (long*)&path[i];
                    *buf = data;
                }
                // we only allow "selects" that immediately returns
                struct timeval* t = (struct timeval*)&path;
                if (t->tv_sec != 0 || t->tv_usec != 0) {
                    break;
                }
            }
            ptrace(PTRACE_SYSCALL, pid, 0, 0);
            return true;
        case SYS_kill:
            if (before_syscall) {
                // allow self-kill
                if ((int) regs.REG_ARG0 != pid || (regs.REG_ARG1 != SIGKILL && regs.REG_ARG1 != SIGFPE))
                    break;
            }
            ptrace(PTRACE_SYSCALL, pid, 0, 0);
            return true;
        case SYS_open:
            if (before_syscall) {
                if (readStringFromTracedProcess(pid, regs.REG_ARG0, path, sizeof(path)) < 0) {
                    break;
                }
                writeLog("SYS_open %s flag %x", path, regs.REG_ARG1);
                if (!allowedFileAccess(path)) {
                    break;
                }
                regs.REG_ARG1 &= ~( O_WRONLY | O_RDWR | O_CREAT | O_APPEND);
                ptrace(PTRACE_SETREGS, pid, 0, &regs);
            }
            ptrace(PTRACE_SYSCALL, pid, 0, 0);
            return true;
    }
    return false;
}

void watchSolution(pid_t pidApp, char* inFile, char* outFile, char* userFile, int &caseResult, int &caseUsedTime, int &caseUsedMemory) {

    caseResult = ACCEPTED;
    caseUsedTime = caseUsedMemory = 0;

    int tmpMemory, status, sig, exitCode;
    struct user_regs_struct regs;
    struct rusage ruse;

    int first_execve = 1, before_syscall = 1;
    unsigned long requested_brk;

    while (1) {
        wait4(pidApp, &status, 0, &ruse);

        // check time
        caseUsedTime = (ruse.ru_utime.tv_sec * 1000 + ruse.ru_utime.tv_usec / 1000);
        caseUsedTime += (ruse.ru_stime.tv_sec * 1000 + ruse.ru_stime.tv_usec / 1000);
        if (caseUsedTime > solutionInfo.timeLimit) {
            caseResult = TIME_EXCEEDED;
            ptrace(PTRACE_KILL, pidApp, NULL, NULL);
            break;
        }

        tmpMemory = getProcStatus(pidApp, "VmPeak:")<<10;
        if (tmpMemory > caseUsedMemory) {
            caseUsedMemory = tmpMemory;
        }

        // MLE
        if (caseUsedMemory > solutionInfo.memoryLimit*STD_KB) {
            caseResult = MEMORY_EXCEEDED;
            ptrace(PTRACE_KILL, pidApp, NULL, NULL);
            break;
        }

        if (WIFEXITED(status)) {
            break;
        }

        // RE
        if (getFilesize("re.txt")) {
            caseResult = RUNTIME_ERROR;
            ptrace(PTRACE_KILL, pidApp, NULL, NULL);
            break;
        }

        // OL
        if (getFilesize(userFile) > getFilesize(outFile)*2+1024) {
            caseResult = OUTPUT_EXCEEDED;
            ptrace(PTRACE_KILL, pidApp, NULL, NULL);
            break;
        }

        exitCode = WEXITSTATUS(status);
        if (exitCode == 0x05 || exitCode == 0) {
            ;
        } else {
            if (caseResult == ACCEPTED) {
                switch (exitCode) {
                    case SIGCHLD:
                    case SIGALRM:
                        alarm(0);
                    case SIGKILL:
                    case SIGXCPU:
                        caseResult = TIME_EXCEEDED;
                        break;
                    case SIGXFSZ:
                        caseResult = OUTPUT_EXCEEDED;
                        break;
                    default:
                        caseResult = RUNTIME_ERROR;
                }
                logRuntimeError(strsignal(exitCode));
            }
            ptrace(PTRACE_KILL, pidApp, NULL, NULL);
            break;
        }

        // WIFSIGNALED: if the process is terminated by signal
        if (WIFSIGNALED(status)) {
            sig = WTERMSIG(status);
            if (caseResult == ACCEPTED) {
                switch (sig) {
                    case SIGCHLD:
                    case SIGALRM:
                        alarm(0);
                    case SIGKILL:
                    case SIGXCPU:
                        caseResult = TIME_EXCEEDED;
                        break;
                    case SIGXFSZ:
                        caseResult = OUTPUT_EXCEEDED;
                        break;
                    default:
                        caseResult = RUNTIME_ERROR;
                }
                logRuntimeError(strsignal(sig));
            }
            break;
        }

        before_syscall = !before_syscall;

        // check the system calls
        ptrace(PTRACE_GETREGS, pidApp, NULL, &regs);

        if (handleSyscall(pidApp, regs, first_execve, before_syscall, caseResult, requested_brk)) {
            continue;
        }

        if (regs.REG_SYSCALL < sizeof(enabled_syscall) / sizeof(enabled_syscall[0])
        && !enabled_syscall[regs.REG_SYSCALL]) {
            caseResult = RUNTIME_ERROR;
            char error[BUF_SIZE];
            sprintf(error, "Restricted syscall %s", syscall_name[regs.REG_SYSCALL]);
            logRuntimeError(error);
            ptrace(PTRACE_KILL, pidApp, NULL, NULL);
            break;
        }
        ptrace(PTRACE_SYSCALL, pidApp, NULL, NULL);
    }

    // fixed
    if (caseResult == TIME_EXCEEDED && caseUsedMemory == 0) {
        caseResult = MEMORY_EXCEEDED;
    }
    if (caseResult == TIME_EXCEEDED && caseUsedTime <= solutionInfo.timeLimit) {
        caseUsedTime = solutionInfo.timeLimit+1;
    }
}

void find_next_nonspace(int & c1, int & c2, FILE *& f1, FILE *& f2, int & ret) {

    // Find the next non-space character or \n.
    while ((isspace(c1)) || (isspace(c2))) {
        if (c1 != c2) {
            if (c2 == EOF) {
                do {
                    c1 = fgetc(f1);
                } while (isspace(c1));
                continue;
            } else if (c1 == EOF) {
                do {
                    c2 = fgetc(f2);
                } while (isspace(c2));
                continue;
            } else if ((c1 == '\r' && c2 == '\n')) {
                c1 = fgetc(f1);
            } else if ((c2 == '\r' && c1 == '\n')) {
                c2 = fgetc(f2);
            } else {
                ret = PRESENTATION_ERROR;
            }
        }
        if (isspace(c1)) {
            c1 = fgetc(f1);
        }
        if (isspace(c2)) {
            c2 = fgetc(f2);
        }
    }
}

const char * getFileNameFromPath(const char * path) {

    for (int i = strlen(path); i >= 0; i--) {
        if (path[i] == '/')
            return &path[i];
    }
    return path;
}

void make_diff_out(FILE *f1, FILE *f2, int c1, int c2, const char * path) {

    FILE *out;
    char buf[45];
    out = fopen("diff.out", "a+");
    fprintf(out, "=================%s\n", getFileNameFromPath(path));
    fprintf(out, "Right:\n%c", c1);
    if (fgets(buf, 44, f1)) {
        fprintf(out, "%s", buf);
    }
    fprintf(out, "\n-----------------\n");
    fprintf(out, "Your:\n%c", c2);
    if (fgets(buf, 44, f2)) {
        fprintf(out, "%s", buf);
    }
    fprintf(out, "\n=================\n");
    fclose(out);
}

// COPY FORM ZOJ
int compare_zoj(const char *file1, const char *file2) {

    int ret = ACCEPTED;
    int c1, c2;
    FILE * f1, *f2;
    f1 = fopen(file1, "r");
    f2 = fopen(file2, "r");
    if (!f1 || !f2) {
        ret = RUNTIME_ERROR;
    } else
        for (;;) {
            // Find the first non-space character at the beginning of line.
            // Blank lines are skipped.
            c1 = fgetc(f1);
            c2 = fgetc(f2);
            find_next_nonspace(c1, c2, f1, f2, ret);
            // Compare the current line.
            for (;;) {
                // Read until 2 files return a space or 0 together.
                while ((!isspace(c1) && c1) || (!isspace(c2) && c2)) {
                    if (c1 == EOF && c2 == EOF) {
                        goto end;
                    }
                    if (c1 == EOF || c2 == EOF) {
                        break;
                    }
                    if (c1 != c2) {
                        // Consecutive non-space characters should be all exactly the same
                        ret = WRONG_ANSWER;
                        goto end;
                    }
                    c1 = fgetc(f1);
                    c2 = fgetc(f2);
                }
                find_next_nonspace(c1, c2, f1, f2, ret);
                if (c1 == EOF && c2 == EOF) {
                    goto end;
                }
                if (c1 == EOF || c2 == EOF) {
                    ret = WRONG_ANSWER;
                    goto end;
                }

                if ((c1 == '\n' || !c1) && (c2 == '\n' || !c2)) {
                    break;
                }
            }
        }
    end: if (ret == WRONG_ANSWER)
        make_diff_out(f1, f2, c1, c2, file1);
    if (f1)
        fclose(f1);
    if (f2)
        fclose(f2);
    return ret;
}

void get_escape_string(const char* filename, char* buf) {

    char text[1<<16], *end;
    int fd = open(filename, O_RDONLY | O_CREAT);
    int len = read(fd, text, 40000);
    text[len] = 0;
    close(fd);

    end = buf;
    end += mysql_real_escape_string(conn, buf, text, strlen(text));
    *end = 0;
}

void update_solution() {

    char sql[1<<18];
    char ceInfo[1<<16], reInfo[1<<16], deInfo[1<<16];

    get_escape_string("ce.txt", ceInfo);
    get_escape_string("re.txt", reInfo);
    get_escape_string("detail.txt", deInfo);

    sprintf(sql
        , "UPDATE oj_judge SET result=%d, time_cost=%d, memory_cost=%d, ce='%s', re='%s', detail='%s' where id=%d"
        , solutionInfo.result, solutionInfo.timeCost, solutionInfo.memoryCost, ceInfo, reInfo, deInfo, solutionInfo.queueId);

    if (mysql_real_query(conn, sql, strlen(sql))) {
        writeLog("update_solution %s", mysql_error(conn));
    } else if (solutionInfo.solutionId > 0) {
        execute_cmd("php /Server/web_hqoj/interface/oj/script/daemon/hqu_judge/sync_hqu_solution.php %d", solutionInfo.solutionId);
    }
}

int main(int argc, char *argv[]) {

    // check argv
    if (argc < 3) {
        writeLog("Need params: queueId, clientId");
        exit(1);
    }
    solutionInfo.queueId  = atoi(argv[1]);
    clientId = atoi(argv[2]);

    // set workDir
    sprintf(workDir, "%s/run%d/", ojHome, clientId);
    if (access(workDir, F_OK) == -1) {
        writeLog("WorkDir: %s is not exist!", workDir);
        exit(1);
    }
    chdir(workDir);
    cleanWorkDir();

    if (!initMysql()) {
        writeLog("initMysql Fail!");
        exit(1);
    }
    if (!getSolutionInfo()) {
        mysql_close(conn);
        writeLog("getSolutionInfo Fail!");
        exit(1);
    }

    // check TimeLimit & MemoryLimit
    if (solutionInfo.timeLimit > MAX_TIME_LIMIT || solutionInfo.timeLimit == 0) {
        solutionInfo.timeLimit = MAX_TIME_LIMIT;
    }
    if (solutionInfo.memoryLimit > MAX_MEMORY_LIMIT || solutionInfo.memoryLimit == 0) {
        solutionInfo.memoryLimit = MAX_MEMORY_LIMIT;
    }

    if (solutionInfo.language != CC && solutionInfo.language != CPLUS) {
        solutionInfo.result = INVALID;
        update_solution();
        mysql_close(conn);
        writeLog("queueId: %d Lang not support!", solutionInfo.queueId);
        return 0;
    }

    // compile
    int compileOk = compile();
    if (compileOk != 0) {
        solutionInfo.result = COMPILATION_ERROR;
        update_solution();
        mysql_close(conn);
        return 0;
    }
    solutionInfo.result = RUNNING;
    update_solution();

    char fullpath[BUF_SIZE];
    sprintf(fullpath, "%s/data/%s", ojHome, solutionInfo.problemId);

    DIR *dp;
    dirent *dirp;
    if ((dp = opendir(fullpath)) == NULL) {
        logDetail("No Data Input");
        solutionInfo.result = INVALID;
        update_solution();
        mysql_close(conn);
        exit(1);
    }

    // init solutionInfo result
    solutionInfo.result     = ACCEPTED;
    solutionInfo.timeCost   = 0;
    solutionInfo.memoryCost = 0;

    char inFile[BUF_SIZE], outFile[BUF_SIZE], userFile[BUF_SIZE];
    int caseUsedTime, caseUsedMemory, caseResult = ACCEPTED;

    char detailRow[BUF_SIZE];

    // get *.in file
    vector<string> inFileList;
    int numOfCase = 0;
    while (((dirp = readdir(dp)) != NULL)) {
        if (isInFile(dirp->d_name) > 0) {
            char *p = strrchr(dirp->d_name, '.');
            int len = strlen(dirp->d_name)-strlen(p);
            char str[20];
            strncpy(str, dirp->d_name, len);
            str[len] = '\0';
            inFileList.push_back(string(str));
            numOfCase++;
        }
    }
    sort(inFileList.begin(), inFileList.end());

    int i;
    for (i = 1; i <= numOfCase && (caseResult == ACCEPTED || caseResult == PRESENTATION_ERROR); i++) {

        caseUsedTime = caseUsedMemory = 0;

        const char *caseFile = inFileList[i-1].c_str();
        if (! prepareFiles(caseFile, inFile, outFile, userFile)) {
            solutionInfo.result = INVALID;
            caseResult = INVALID;
            // log detail
            sprintf(detailRow
                , "Case: %02d  File: %10s.in  %5dMS  %6dKB  %s"
                , i, caseFile
                , caseUsedTime, caseUsedMemory>>10, resultFmt[caseResult]);
            logDetail(detailRow);
            break;
        }

        pid_t pidApp = fork();
        if (pidApp == 0) {
            runSolution();
        } else {
            // watch
            watchSolution(pidApp, inFile, outFile, userFile, caseResult, caseUsedTime, caseUsedMemory);

            // compare
            if (caseResult == ACCEPTED) {
                caseResult = compare_zoj(outFile, userFile);
            }

            // log detail
            sprintf(detailRow
                , "Case: %02d  File: %10s.in  %5dMS  %6dKB  %s"
                , i, caseFile
                , caseUsedTime, caseUsedMemory>>10, resultFmt[caseResult]);
            logDetail(detailRow);

            // record final result
            if (caseResult != ACCEPTED) {
                solutionInfo.result = caseResult;
            }
            if (caseUsedTime > solutionInfo.timeCost) {
                solutionInfo.timeCost = caseUsedTime;
            }
            if ((caseUsedMemory>>10) > solutionInfo.memoryCost) {
                solutionInfo.memoryCost = (caseUsedMemory>>10);
            }
        }
    }

    if (numOfCase == 0) {
        logDetail("No Data Input");
        solutionInfo.result = INVALID;
        update_solution();
        mysql_close(conn);
        exit(1);
    }

    update_solution();
    mysql_close(conn);
    // writeLog("qid: %d  cid: %d  Case: %2d  %5dMS  %6dKB  %s"
    //            , solutionInfo.queueId, clientId, numOfCase
    //            , solutionInfo.timeCost, solutionInfo.memoryCost, resultFmt[solutionInfo.result]);

    return 0;
}
#include <unistd.h>
#include <sys/wait.h>
#include <sys/ptrace.h>
#include <cstdio>
#include <cstdlib>
#include "judger.h"
#include "util.h"
#include "global.h"

Judger::Judger(string dir): workDir(dir) {

    // permission control
    chdir(workDir.c_str());
    executeCMD("chown judge -R %s", workDir.c_str());
    while(setgid(1536)!=0) sleep(1);
    while(setuid(1536)!=0) sleep(1);
    while(setresuid(1536, 1536, 1536)!=0) sleep(1);
}

void Judger::cleanWorkDir() {

    if (workDir.size() == 0) {
        return ;
    }
    if (workDir.find(JUDGE_HOME) != 0) {
        return ;
    }
    executeCMD("/bin/rm -Rf %s/*", workDir.c_str());
}

bool Judger::compile(Solution *solution) {

    const char * CP_CC[]= { "gcc", "Main.c", "-o", "Main", "-fno-asm", "-Wall",
            "-lm", "--static", "-std=c99", "-DONLINE_JUDGE", NULL };
    const char * CP_CXX[]= { "g++", "Main.cc", "-o", "Main", "-fno-asm", "-Wall",
            "-lm", "--static", "-DONLINE_JUDGE", NULL };

    int lang = solution->language;
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

        switch (lang) {
            case CC:
                execvp(CP_CC[0], (char * const *) CP_CC);
                break;
            case CXX:
                execvp(CP_CXX[0], (char * const *) CP_CXX);
                break;
            default:
                exit(1);
        }
        exit(0);
    } else {
        int status = 0;
        waitpid(pid, &status, 0);
        return status > 0 ? false : true;
    }
}

void Judger::run(Solution *solution) {

    nice(19);

    freopen("data.in", "r", stdin);
    freopen("user.out", "w", stdout);
    freopen("re.txt", "a+", stderr);
    
    ptrace(PTRACE_TRACEME, 0, NULL, NULL);

    // time limit
    struct rlimit LIM;
    LIM.rlim_cur = solution->timeLimit/1000;
    LIM.rlim_max = LIM.rlim_cur;
    setrlimit(RLIMIT_CPU, &LIM);
    alarm(0);
    alarm(solution->timeLimit/100);

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

    switch (solution->language) {
        case CC:
        case CXX:
            execl("./Main", "./Main", (char *) NULL);
            break;
    }
    exit(0);

}

void Judger::logRuntimeError(char *err) {

    FILE *ferr = fopen("re.txt", "a+");
    fprintf(ferr, "Runtime Error: %s\n", err);
    fclose(ferr);
}

void Judger::logDetail(char *info) {

    FILE *fh = fopen("detail.txt", "a+");
    if (fh == NULL) {
        printf("no\n");
    }
    fprintf(fh, "%s\n", info);

    fclose(fh);
}
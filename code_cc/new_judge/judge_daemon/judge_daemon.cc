#include <sys/resource.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/wait.h>
#include <errno.h>
#include <fcntl.h>
#include <stdarg.h>
#include <time.h>
#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <signal.h>
#include <mysql/mysql.h>
#include "global.h"

#define LOCKMODE (S_IRUSR|S_IWUSR|S_IRGRP|S_IROTH)
#define MAX_RUNNING 2

static char lockFile[BUF_SIZE] = "/home/judge/etc/judge.pid";

// mysql conf
static char hostname[BUF_SIZE] = "192.168.56.102";
static char username[BUF_SIZE] = "root";
static char password[BUF_SIZE] = "123";
// static char hostname[BUF_SIZE] = "hqojmaster.mysql.rds.aliyuncs.com";
// static char username[BUF_SIZE] = "aozhongxu";
// static char password[BUF_SIZE] = "rsdd1_23";
static char dbname[BUF_SIZE]   = "web_hqoj";
static int  port               = 3306;
MYSQL *conn;

void call_for_exit(int s) {
    printf("Stopping judged...\n");
    exit(0);
}

void writeLog(const char *fmt, ...) {

    char buffer[4096];
    sprintf(buffer, "%s/log/deamon.log", JUDGE_HOME.c_str());
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
    fprintf(fp, "%s %s\n", timeStr, buffer);
    va_end(ap);
    fclose(fp);
}

int executesql(const char * sql) {

    if (mysql_real_query(conn, sql, strlen(sql))) {
        writeLog("executesql %s", mysql_error(conn));
        sleep(20);
        conn = NULL;
        return 1;
    } else {
        return 0;
    }
}

int initMysql() {

    if (conn == NULL) {
        conn = mysql_init(NULL);
        const char timeout = 30;
        mysql_options(conn, MYSQL_OPT_CONNECT_TIMEOUT, &timeout);
        if (!mysql_real_connect(conn, hostname, username, password, dbname, port, 0, 0)) {
            writeLog("initMysql %s", mysql_error(conn));
            return 1;
        }
        return executesql("set names utf8");
    }
    return 0;
}

int getJobs(int *jobs) {

    MYSQL_RES *res;
    MYSQL_ROW row;
    int i;
    for (i = 0; i <= MAX_RUNNING*2; i++) {
        jobs[i] = 0;
    }
    char sql[BUF_SIZE];
    sprintf(sql
        , "SELECT queue_id FROM oj_judge_queue WHERE result<2 ORDER BY result ASC, queue_id ASC limit %d"
        , MAX_RUNNING * 2);
    if (mysql_real_query(conn, sql, strlen(sql))) {
        writeLog("getJobs %s", mysql_error(conn));
        sleep(10);
        return 0;
    }
    res = mysql_store_result(conn);
    i = 0;
    while ((row = mysql_fetch_row(res)) != NULL) {
        jobs[i++] = atoi(row[0]);
    }
    mysql_free_result(res);
    return i;
}

bool updateSolution(int queueId, int result) {

    time_t rawtime;
    time(&rawtime);
    char sql[BUF_SIZE];
    sprintf(sql
        , "UPDATE oj_judge_queue SET result=%d,time_cost=0,memory_cost=0,judge_time=%ld,ce='',re='',detail='' WHERE queue_id=%d AND result<2 LIMIT 1"
        , result, rawtime, queueId);
    if (mysql_real_query(conn, sql, strlen(sql))) {
        writeLog("updateSolution %s", mysql_error(conn));
        return false;
    } else {
        return mysql_affected_rows(conn) > 0ul ? true : false;
    }
}

void run_client(int queueId, int clientId) {

    struct rlimit LIM;
    LIM.rlim_max = 800;
    LIM.rlim_cur = 800;
    setrlimit(RLIMIT_CPU, &LIM);

    LIM.rlim_max = 80 * STD_MB;
    LIM.rlim_cur = 80 * STD_MB;
    setrlimit(RLIMIT_FSIZE, &LIM);

    LIM.rlim_max = STD_MB << 11;
    LIM.rlim_cur = STD_MB << 11;
    setrlimit(RLIMIT_AS, &LIM);

    LIM.rlim_cur = LIM.rlim_max = 200;
    setrlimit(RLIMIT_NPROC, &LIM);

    char queueIdStr[BUF_SIZE], clientIdStr[BUF_SIZE];
    sprintf(queueIdStr, "%d", queueId);
    sprintf(clientIdStr, "%d", clientId);

    execl("/Server/web_hqoj/code_cc/judge/client/client"
        , "/Server/web_hqoj/code_cc/judge/client/client"
        , queueIdStr, clientIdStr, (char *) NULL);
}

int work() {

    static int retcnt = 0;
    static int workcnt  = 0;
    static pid_t PID[MAX_RUNNING];
    int queueId, i, j;
    int jobs[MAX_RUNNING*2+1];
    pid_t tmpPid = 0;

    if (!getJobs(jobs)) {
        retcnt = 0;
    }
    
    for (j = 0; jobs[j]; j++) {
        queueId = jobs[j];
        if (workcnt >= MAX_RUNNING) {
            tmpPid = waitpid(-1, NULL, 0);
            workcnt--;
            retcnt++;
            for (i = 0; i < MAX_RUNNING; i++) {
                if (PID[i] == tmpPid) {
                    break;
                }
            }
            PID[i] = 0;
        } else {
            for (i = 0; i < MAX_RUNNING; i++) {
                if (PID[i] == 0) {
                    break;
                }
            }
        }
        if (workcnt < MAX_RUNNING && updateSolution(queueId, COMPILING)) {
            workcnt++;
            PID[i] = fork();
            if (PID[i] == 0) {
                run_client(queueId, i);
                exit(0);
            }
        }
    }
    // wait remaining procs
    while ((tmpPid = waitpid(-1, NULL, WNOHANG)) > 0) {
        workcnt--;
        retcnt++;
        for (i = 0; i < MAX_RUNNING; i++) {
            if (PID[i] == tmpPid) {
                break;
            }
        }
        PID[i] = 0;
    }
    return retcnt;
}

int lock_file(int fd) {
    struct flock fl;
    fl.l_type   = F_WRLCK;
    fl.l_start  = 0;
    fl.l_whence = SEEK_SET;
    fl.l_len    = 0;
    return (fcntl(fd, F_SETLK, &fl));
}

int alreadyRunning() {

    int fd;
    char buf[16];
    fd = open(lockFile, O_RDWR | O_CREAT, LOCKMODE);
    if (fd < 0) {
        writeLog("can't open %s: %s", lockFile, strerror(errno));
        exit(1);
    }
    if (lock_file(fd) < 0) {
        if (errno == EACCES || errno == EAGAIN) {
            close(fd);
            return 1;
        }
        writeLog("can't lock %s: %s", lockFile, strerror(errno));
        exit(1);
    }
    ftruncate(fd, 0);
    sprintf(buf, "%d", getpid());
    write(fd, buf, strlen(buf));
    return 0;
}

int deamonInit() {

    pid_t pid;
    if ((pid = fork()) < 0) {
        return -1;
    } else if (pid != 0) {
        exit(0);
    }
    setsid();
    chdir(JUDGE_HOME.c_str());
    umask(0);
    close(0);
    close(1);
    close(2);
    return 0;
}

int main() {

    chdir(JUDGE_HOME.c_str());
    deamonInit();
    if (alreadyRunning()) {
        writeLog("This daemon program is already running!");
        return 1;
    }
    signal(SIGQUIT, call_for_exit);
    signal(SIGKILL, call_for_exit);
    signal(SIGTERM, call_for_exit);
    int j = 1;
    while (1) {
        while (j && (0 == initMysql())) {
            j = work();
        }
        sleep(1);
        j = 1;
    }
    
    return 0;
}
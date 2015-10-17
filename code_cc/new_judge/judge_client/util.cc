#include <cstdarg>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include "util.h"

string getLocalTimeAsString(const char *format) {
    time_t t = time(NULL);
    struct tm tm;
    localtime_r(&t, &tm);
    char buf[1024];
    strftime(buf, sizeof(buf), format, &tm);
    return buf;
}

void trim(char *str) {

    char buf[4096];
    char *start, *end;
    strcpy(buf, str);
    start = buf;
    while (isspace(*start)) start++;
    end = start;
    while (*end && !isspace(*end)) end++;
    *end = '\0';
    strcpy(str, start);
}

string getFileWithEscape(MYSQL *conn, const char *filename) {

    if (access(filename, F_OK) == -1) {
        return "";
    }
    char text[1<<16], buf[1<<16], *end;
    int fd = open(filename, O_RDONLY | O_CREAT);
    int len = read(fd, text, 1<<16);
    text[len] = 0;
    close(fd);

    end = buf;
    end += mysql_real_escape_string(conn, buf, text, strlen(text));
    *end = 0;
    return string(buf);
}

int executeCMD(const char *fmt, ...) {

    char cmd[1024];
    int ret = 0;
    va_list ap;
    va_start(ap, fmt);
    vsprintf(cmd, fmt, ap);
    ret = system(cmd);
    va_end(ap);
    return ret;
}

long getFilesize(const char *filename) {

    struct stat f_stat;
    if (stat(filename, &f_stat) == -1) {
        return 0;
    }
    return (long) f_stat.st_size;
}

string getFileExt(const char *filename) {

    const char *p = strrchr(filename, '.');
    if (p == NULL) {
        return "";
    }
    return string(++p);
}
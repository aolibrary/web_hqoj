#include <cstdio>
#include "logger.h"
#include "util.h"

void Logger::write(string path, const char *format, ...) {

    FILE *fp = fopen(path.c_str(), "a+");
    if (fp == NULL) {
        return ;
    }

    char buffer[1024];
    va_list args;
    va_start(args, format);
    vsprintf(buffer, format, args);
    fprintf(fp, "%s %s\n", getLocalTimeAsString("%Y-%m-%d %H:%M:%S").c_str(), buffer);
    va_end(args);
    fclose(fp);
}

void Logger::write(string path, const char *format, va_list args) {

    FILE *fp = fopen(path.c_str(), "a+");
    if (fp == NULL) {
        return ;
    }

    char buffer[1024];
    vsprintf(buffer, format, args);
    fprintf(fp, "%s %s\n", getLocalTimeAsString("%Y-%m-%d %H:%M:%S").c_str(), buffer);
    fclose(fp);
}

string ClientLogger::path = "/home/judge/log/client.log";

void ClientLogger::write(const char *format, ...) {

    va_list args;
    va_start(args, format);
    Logger::write(path, format, args);
    va_end(args);
}
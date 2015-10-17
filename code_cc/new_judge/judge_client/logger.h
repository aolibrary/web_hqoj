#ifndef __LOGGER_H__
#define __LOGGER_H__

#include <string>
#include <cstdarg>
using namespace std;

class Logger {
public:
    static void write(string path, const char *format, ...);
protected:
    static void write(string path, const char *format, va_list args);
};

class ClientLogger: public Logger {
private:
    static string path;
public:
    static void write(const char *format, ...);
};

#endif
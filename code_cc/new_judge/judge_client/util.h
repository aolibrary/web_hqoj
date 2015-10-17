#ifndef __UTIL_H__
#define __UTIL_H__

#include <string>
#include <mysql/mysql.h>
using namespace std;

string getLocalTimeAsString(const char *format);
void trim(char *c);
string getFileWithEscape(MYSQL *conn, const char *filename);
int executeCMD(const char *fmt, ...);
long getFilesize(const char *filename);
string getFileExt(const char *filename);

#endif
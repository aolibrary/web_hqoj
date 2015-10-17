#ifndef __JUDGER_H__
#define __JUDGER_H__

#include <string>
#include "solution.h"
using namespace std;

class Judger {
public:
    Judger(string dir);
    void cleanWorkDir();
    bool compile(Solution *solution);
    void run(Solution *solution);
    static void logRuntimeError(char* err);
    static void logDetail(char* info);

private:
    string workDir;
};

#endif
#ifndef __GLOBAL_H__
#define __GLOBAL_H__

#include <string>
using namespace std;

static string JUDGE_HOME = "/home/judge";
static string LANG_EXT[10] = { "", "c", "cc", "c", "cc" };

static char RESULT_FMT[14][50] = {
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

#define CC      3
#define CXX     4

#define BUF_SIZE            512
#define STD_KB              1024
#define STD_MB              1048576
#define STD_F_LIM           (STD_MB<<5) // 32M
#define MAX_TIME_LIMIT      10000       // 10s
#define MAX_MEMORY_LIMIT    256*1024    // 256M
#define MAX_AS_LIMIT        600         // set AS 600M

#endif
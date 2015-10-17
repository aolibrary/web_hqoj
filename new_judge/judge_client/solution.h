#ifndef __SOLUTION_H__
#define __SOLUTION_H__

#include <string>
#include <mysql/mysql.h>
using namespace std;

class Solution {
public:
    Solution(int qid, string dir);
    bool update();

    string workDir;
    int queueId;
    string problemId;
    int language;
    int timeLimit;
    int memoryLimit;
    int solutionId;

    // result
    int timeCost, memoryCost, result;
    
private:
    static MYSQL *conn;
    bool initMysql();
    bool fetch();
};

#endif
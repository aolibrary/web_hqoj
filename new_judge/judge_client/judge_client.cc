#include <iostream>
#include <string>
#include <cstdio>
#include <cstring>
#include <cstdlib>
#include <dirent.h>
#include "config.h"
#include "solution.h"
#include "global.h"
#include "judger.h"
#include "logger.h"
#include "case.h"
#include "tracer.h"
#include "util.h"
using namespace std;

int main(int argc, char *argv[]) {

    // check argv
    if (argc < 3) {
        ClientLogger::write("Need params: queueId, clientId");
        return 1;
    }

    int queueId  = atoi(argv[1]);
    int clientId = atoi(argv[2]);

    string workDir = Config::getClientWorkDir(clientId);
    if (workDir.empty()) {
        return 1;
    }

    Judger *judger = new Judger(workDir);
    judger->cleanWorkDir();

    Solution *solution = new Solution(queueId, workDir);
    if (solution->result == INVALID) {
        return 1;
    }

    if (false == judger->compile(solution)) {
        solution->result = COMPILATION_ERROR;
        solution->update();
        return 1;
    }
    solution->result = RUNNING;
    solution->update();

    string dataPath = JUDGE_HOME+"/data/"+solution->problemId;

    DIR *dp;
    if (NULL == (dp = opendir(dataPath.c_str()))) {
        ClientLogger::write("No suchdir:%s", dataPath.c_str());
        solution->result = INVALID;
        solution->update();
        return 1;
    }

    // compute the number of *.in
    dirent *dirp;
    int numOfCase = 0;
    while (((dirp = readdir(dp)) != NULL)) {
        if (getFileExt(dirp->d_name) == "in") {
            numOfCase++;
        }
    }

    if (numOfCase == 0) {
        ClientLogger::write("%s", "No Data Input!");
        solution->result = INVALID;
        solution->update();
        return 1;
    }

    char detailRow[1024];
    
    for (int i = 1; i <= numOfCase; i++) {

        char buf[2];
        sprintf(buf, "%02d", i);

        Case *oneCase = new Case(string(buf), solution->problemId, workDir);
        if (false == oneCase->moveFiles()) {
            oneCase->result = INVALID;
            solution->result = INVALID;
            sprintf(detailRow
                , "Case: %02d  File: %02d.in  %5dMS  %6dKB  %s"
                , i, i
                , oneCase->usedTime, (oneCase->usedMemory)>>10, RESULT_FMT[oneCase->result]);
            Judger::logDetail(detailRow);
            delete oneCase;
            break;
        }

        pid_t pid = fork();
        if (0 == pid) {
            judger->run(solution);
            exit(0);
        } else {
            Tracer *tracer = new Tracer(pid);
            tracer->watch(oneCase, solution);
            delete tracer;

            if (oneCase->result == ACCEPTED) {
                oneCase->compare();
            }
            sprintf(detailRow
                , "Case: %02d  File: %02d.in  %5dMS  %6dKB  %s"
                , i, i
                , oneCase->usedTime, (oneCase->usedMemory)>>10, RESULT_FMT[oneCase->result]);
            // cout<<detailRow<<endl;
            Judger::logDetail(detailRow);

            // record final result
            solution->result = oneCase->result;
            if (oneCase->usedTime > solution->timeCost) {
                solution->timeCost = oneCase->usedTime;
            }
            if ((oneCase->usedMemory>>10) > solution->memoryCost) {
                solution->memoryCost = (oneCase->usedMemory>>10);
            }

            delete oneCase;
        }

        if (solution->result != ACCEPTED && solution->result != PRESENTATION_ERROR) {
            break;
        }
    }

    solution->update();

    return 0;
}
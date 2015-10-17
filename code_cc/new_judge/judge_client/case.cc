#include <cstdio>
#include <iostream>
#include "case.h"
#include "global.h"
#include "util.h"

Case::Case(string name, string proId, string dir)
: casename(name), problemId(proId), workDir(dir)
, usedTime(0) , usedMemory(0), result(ACCEPTED) {

    char str[1024];
    sprintf(str, "%s/data/%s/%s", JUDGE_HOME.c_str(), problemId.c_str(), casename.c_str());
    inFile = string(str)+".in";
    outFile = string(str)+".out";
    userFile = workDir+"/user.out";
}

bool Case::moveFiles() {

    if (access(inFile.c_str(), F_OK) == -1) {
        return false;
    } else {
        int ret = executeCMD("/bin/cp %s %s/data.in", inFile.c_str(), workDir.c_str());
        return ret == 0 ? true : false;
    }
}

void Case::compare() {

    int c1, c2;
    FILE * f1, *f2;
    f1 = fopen(outFile.c_str(), "r");
    f2 = fopen(userFile.c_str(), "r");
    if (!f1 || !f2) {
        result = RUNTIME_ERROR;
    } else
        for (;;) {
            // Find the first non-space character at the beginning of line.
            // Blank lines are skipped.
            c1 = fgetc(f1);
            c2 = fgetc(f2);
            find_next_nonspace(c1, c2, f1, f2);
            // Compare the current line.
            for (;;) {
                // Read until 2 files return a space or 0 together.
                while ((!isspace(c1) && c1) || (!isspace(c2) && c2)) {
                    if (c1 == EOF && c2 == EOF) {
                        goto end;
                    }
                    if (c1 == EOF || c2 == EOF) {
                        break;
                    }
                    if (c1 != c2) {
                        // Consecutive non-space characters should be all exactly the same
                        result = WRONG_ANSWER;
                        goto end;
                    }
                    c1 = fgetc(f1);
                    c2 = fgetc(f2);
                }
                find_next_nonspace(c1, c2, f1, f2);
                if (c1 == EOF && c2 == EOF) {
                    goto end;
                }
                if (c1 == EOF || c2 == EOF) {
                    result = WRONG_ANSWER;
                    goto end;
                }

                if ((c1 == '\n' || !c1) && (c2 == '\n' || !c2)) {
                    break;
                }
            }
        }
    end:
    if (f1)
        fclose(f1);
    if (f2)
        fclose(f2);
}

void Case::find_next_nonspace(int & c1, int & c2, FILE *& f1, FILE *& f2) {

    // Find the next non-space character or \n.
    while ((isspace(c1)) || (isspace(c2))) {
        if (c1 != c2) {
            if (c2 == EOF) {
                do {
                    c1 = fgetc(f1);
                } while (isspace(c1));
                continue;
            } else if (c1 == EOF) {
                do {
                    c2 = fgetc(f2);
                } while (isspace(c2));
                continue;
            } else if ((c1 == '\r' && c2 == '\n')) {
                c1 = fgetc(f1);
            } else if ((c2 == '\r' && c1 == '\n')) {
                c2 = fgetc(f2);
            } else {
                result = PRESENTATION_ERROR;
            }
        }
        if (isspace(c1)) {
            c1 = fgetc(f1);
        }
        if (isspace(c2)) {
            c2 = fgetc(f2);
        }
    }
}


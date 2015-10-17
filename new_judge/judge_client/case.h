#ifndef __CASE_H__
#define __CASE_H__

#include <string>
using namespace std;

class Case {
public:
    Case(string name, string proId, string dir);
    bool moveFiles();
    void compare();

    string casename, problemId, workDir;
    string inFile, outFile, userFile;
    int usedTime, usedMemory, result;
private:
    void find_next_nonspace(int & c1, int & c2, FILE *& f1, FILE *& f2);
};

#endif
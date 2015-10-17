#ifndef __CONFIG_H__
#define __CONFIG_H__

#include <string>
#include <map>
using namespace std;

class Config {
public:
    static map<string, string> getJudgeConfig();
    static string getClientWorkDir(int clientId);
private:
    static map<string, string> config;
};

#endif
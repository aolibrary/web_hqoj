#include <cstdio>
#include <cstring>
#include "config.h"
#include "global.h"
#include "util.h"
#include "logger.h"

map<string, string> Config::config;

map<string, string> Config::getJudgeConfig() {

    if (config.empty()) {
        string path = JUDGE_HOME + "/etc/judge.conf";
        FILE *fp = fopen(path.c_str(), "r");
        char buf[1024], key[1024], value[1024];
        char *p;
        int len;
        while (fgets(buf, 1024, fp) != NULL) {
            p = strchr(buf, '=');
            len = strlen(buf)-strlen(p);
            strncpy(key, buf, len);
            key[len] = '\0';
            trim(key);
            strcpy(value, ++p);
            trim(value);
            config[key] = value;
        }
    }
    return config;
}

string Config::getClientWorkDir(int clientId) {

    string workDir;
    char buf[2];
    sprintf(buf, "%d", clientId);
    workDir = JUDGE_HOME+"/run"+buf+"/";
    if (access(workDir.c_str(), F_OK) == -1) {
        ClientLogger::write("WorkDir: %s is not exist!", workDir.c_str());
        return "";
    }
    return workDir;
}
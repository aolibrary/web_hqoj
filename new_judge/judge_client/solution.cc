#include <cstdio>
#include <string>
#include <map>
#include <cstring>
#include <cstdlib>
#include "solution.h"
#include "logger.h"
#include "config.h"
#include "global.h"
#include "util.h"

MYSQL* Solution::conn = NULL;

Solution::Solution(int qid, string dir): queueId(qid), workDir(dir) {

    if (false == fetch()) {
        result = INVALID;
        return ;
    }

    // check some params
    if (timeLimit > MAX_TIME_LIMIT || timeLimit == 0) {
        timeLimit = MAX_TIME_LIMIT;
    }
    if (memoryLimit > MAX_MEMORY_LIMIT || memoryLimit == 0) {
        memoryLimit = MAX_MEMORY_LIMIT;
    }

    // init
    timeCost   = 0;
    memoryCost = 0;
}

bool Solution::initMysql() {

    map<string, string> conf = Config::getJudgeConfig();

    conn = mysql_init(NULL);
    const char timeout = 30;
    mysql_options(conn, MYSQL_OPT_CONNECT_TIMEOUT, &timeout);
    if (!mysql_real_connect(conn, conf["mysql_hostname"].c_str(), conf["mysql_username"].c_str()
        , conf["mysql_password"].c_str(), conf["mysql_dbname"].c_str(), atoi(conf["mysql_port"].c_str()), 0, 0)) {
        ClientLogger::write("%s", mysql_error(conn));
        return false;
    }
    const char * utf8sql = "set names utf8";
    if (mysql_real_query(conn, utf8sql, strlen(utf8sql))) {
        ClientLogger::write("%s", mysql_error(conn));
        return false;
    }
    return true;
}

bool Solution::fetch() {

    initMysql();

    MYSQL_RES *res;
    MYSQL_ROW row;
    char sql[1024];
    sprintf(sql
        , "SELECT problem_id, language, time_limit, memory_limit, source, solution_id, result FROM oj_judge_queue where queue_id=%d"
        , queueId);
    if (mysql_real_query(conn, sql, strlen(sql)) != 0) {
        ClientLogger::write("%s", mysql_error(conn));
        return false;
    }
    res = mysql_store_result(conn);
    row = mysql_fetch_row(res);
    
    if (!row) {
        ClientLogger::write("queueId %d: %s", queueId, "Solution is not existed!");
        return false;
    }
    problemId   = row[0];
    language    = atoi(row[1]);
    timeLimit   = atoi(row[2]);
    memoryLimit = atoi(row[3]);
    solutionId  = atoi(row[5]);
    result      = atoi(row[6]);

    if (result != QUEUE && result != REJUDGE) {
    //    ClientLogger::write("queueId %d: %s", queueId, "Solution is not need judge!");
    //    return false;
    }

    // Save Source
    string srcPath = workDir + "/Main." + LANG_EXT[language];
    FILE *fp = fopen(srcPath.c_str(), "w");
    fprintf(fp, "%s", row[4]);
    fclose(fp);

    mysql_free_result(res);

    return true;
}

bool Solution::update() {

    char sql[1<<18];
    string ceInfo, reInfo, deInfo;

    string filename = workDir+"/ce.txt";
    ceInfo = getFileWithEscape(conn, filename.c_str());
    filename = workDir+"/re.txt";
    reInfo = getFileWithEscape(conn, filename.c_str());
    filename = workDir+"/detail.txt";
    deInfo = getFileWithEscape(conn, filename.c_str());

    sprintf(sql
        , "UPDATE oj_judge_queue SET result=%d, time_cost=%d, memory_cost=%d, ce='%s', re='%s', detail='%s' where queue_id=%d"
        , result, timeCost, memoryCost, ceInfo.c_str(), reInfo.c_str(), deInfo.c_str(), queueId);

    if (mysql_real_query(conn, sql, strlen(sql)) != 0) {
        ClientLogger::write("%s", mysql_error(conn));
        return false;
    }
    if (solutionId > 0) {
        executeCMD("php /Server/web_hqoj/abs/script/daemon/hqu_judge/sync_hqu_solution.php %d", solutionId);
    }
    return true;
}
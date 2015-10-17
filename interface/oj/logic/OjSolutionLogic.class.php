<?php

require_once __DIR__ . '/../model/OjSolutionModel.class.php';

class OjSolutionLogic {

    public static function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0, $includeContest = false) {
        if (!$includeContest) {
            $where[] = array('contest_id', '=', 0);
        }
        $model = new OjSolutionModel();
        return $model->getList($field, $where, $order, $limit, $offset);
    }

    public static function getCount($where = array(), $includeContest = false) {
        if (!$includeContest) {
            $where[] = array('contest_id', '=', 0);
        }
        $model = new OjSolutionModel();
        return $model->getCount($where);
    }

    public static function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {
        $model = new OjSolutionModel();
        return $model->getRow($field, $where, $order, $offset);
    }

    public static function getById($id) {
        $model = new OjSolutionModel();
        return $model->getById($id);
    }

    public static function getByField($field, $value) {
        $model = new OjSolutionModel();
        return $model->getByField($field, $value);
    }

    public static function save($data, $id = 0, $trans = null) {

        if (0 == $id) {

            $globalId = $data['global_id'];
            $language = $data['language'];
            $userId   = $data['user_id'];
            $source   = $data['source'];
            $contestId = Arr::get('contest_id', $data, 0);

            // 设置默认语言
            Cookie::set('default_language', $language, time()+365*86400);

            $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
            if (empty($problemInfo)) {
                throw new InterfaceException('题目不存在！');
            }
            if (!array_key_exists($data['language'], StatusVars::$LANGUAGE_SUPPORT[$problemInfo['remote']])) {
                throw new InterfaceException('编译器不支持！');
            }

            // 连续提交判断
            if (self::submitAlready($userId)) {
                throw new InterfaceException('提交频繁！');
            }

            // 非法字符判断
            if ($problemInfo['remote'] == StatusVars::REMOTE_HDU) {
                if (false === iconv('UTF-8', 'GBK', $source)) {
                    throw new InterfaceException('代码中存在非法字符！');
                }
            }

            // 开始事务
            $innerTrans = $trans;
            if (null == $trans) {
                $innerTrans = new Trans(DbConfig::$SERVER_TRANS);
                $innerTrans->begin();
            }

            $solutionModel = new OjSolutionModel($innerTrans);

            $submitTime = time();
            $data = array(
                'problem_global_id' => $globalId,
                'remote'            => $problemInfo['remote'],
                'problem_id'        => $problemInfo['problem_id'],
                'problem_code'      => $problemInfo['problem_code'],
                'user_id'           => $userId,
                'submit_time'       => $submitTime,
                'submit_ip'         => Http::getClientIp(),
                'language'          => $language,
                'result'            => StatusVars::QUEUE,
                'code_length'       => strlen($source),
                'remote_uid'        => -1,
            );

            $userInfo = UserCommonInterface::getById(array('id' => $userId));

            if (0 == $contestId) {
                $solutionId = $solutionModel->insert($data);
                UserCommonInterface::save(array(
                    'trans'         => $innerTrans,
                    'id'            => $userId,
                    'submit_all'    => $userInfo['submit_all']+1,
                ));
                OjProblemInterface::save(array(
                    'trans'     => $innerTrans,
                    'id'        => $problemInfo['id'],
                    'submit'    => $problemInfo['submit'],
                ));
            } else {
                $contestInfo = OjContestInterface::getDetail(array('id' => $contestId));
                if (empty($contestInfo)) {
                    throw new InterfaceException('竞赛不存在！');
                }
                if ($submitTime > $contestInfo['end_time']) {
                    throw new InterfaceException('比赛已经结束！');
                }
                if ($submitTime < $contestInfo['begin_time']) {
                    throw new InterfaceException('比赛未开始！');
                }
                // 获取当前的submit_order
                $lastRow = OjSolutionInterface::getRow(array(
                    'where' => array(
                        array('contest_id', '=', $contestId),
                    ),
                    'order' => array('id' => 'DESC'),
                ));
                $data['contest_id']            = $contestId;
                $data['contest_submit_order']  = empty($lastRow) ? 1 : intval($lastRow['contest_submit_order'])+1;
                $data['contest_submit_second'] = $submitTime-$contestInfo['begin_time'];
                $data['contest_end_time']      = $contestInfo['end_time'];

                $solutionId = $solutionModel->insert($data);

                // 激活比赛
                if (!$contestInfo['is_active']) {
                    OjContestInterface::save(array(
                        'id'        => $contestId,
                        'trans'     => $innerTrans,
                        'is_active' => 1,
                    ));
                }
            }

            // 如果是HQU题库，加入队列
            if ($problemInfo['remote'] == StatusVars::REMOTE_HQU) {
                OjJudgeInterface::save(array(
                    'trans'       => $innerTrans,
                    'problem_id'  => $problemInfo['problem_id'],
                    'language'    => $language,
                    'source'      => $source,
                    'user_id'     => $userId,
                    'solution_id' => $solutionId,
                ));
            }

            // 保存代码
            OjSolutionCodeInterface::save(array(
                'trans'         => $innerTrans,
                'solution_id'   => $solutionId,
                'source'        => $source,
            ));

            if (null == $trans && null != $innerTrans) {  // 没有外部事务，并且存在内部事务
                $innerTrans->commit();
            }

            // 设置重复提交缓存
            $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
            $key = MemcachedKeys::OJ_SECOND_SUBMIT_ . $userId;
            $memcached->set($key, true, time()+5);

            return $solutionId;
        } else {

            // 开始事务
            $innerTrans = $trans;
            if (null == $trans && array_key_exists('result', $data)) {   // 开启内部事务条件
                $innerTrans = new Trans(DbConfig::$SERVER_TRANS);
                $innerTrans->begin();
            }

            $model = new OjSolutionModel($innerTrans);

            if (array_key_exists('result', $data)) {
                self::updateResult($id, $data['result'], $innerTrans);
                unset($data['result']);
            }

            // 对某些字段特殊处理
            $updateData = $data;
            $affects = $model->updateById($id, $updateData);

            if (null == $trans && null != $innerTrans) {  // 没有外部事务，并且存在内部事务
                $innerTrans->commit();
            }

            return $affects;
        }
    }

    public static function deleteById($id) {
        $model  = new OjSolutionModel();
        return $model->deleteById($id);
    }

    public static function submitAlready($userId) {
        $memcached = MemcachedPool::getMemcached(MemcachedConfig::$SERVER_COMMON);
        $key = MemcachedKeys::OJ_SECOND_SUBMIT_ . $userId;
        $ret = $memcached->get($key);
        return $ret ? true : false;
    }

    public static function getDetail($id) {

        $solutionInfo = self::getById($id);
        if (empty($solutionInfo)) {
            return array();
        }
        $sourceInfo = OjSolutionCodeInterface::getByField(array('solution_id' => $id));
        $solutionInfo['has_log'] = OjSolutionHelper::hasLog($solutionInfo);
        $logInfo = array();
        if ($solutionInfo['has_log']) {
            $logInfo = OjSolutionLogInterface::getByField(array('solution_id' => $id));
        }
        $solutionInfo['source'] = Arr::get('source', $sourceInfo, '', true);
        $solutionInfo['source_format'] = htmlspecialchars($solutionInfo['source'], ENT_COMPAT, 'UTF-8');
        $solutionInfo['ce']     = Arr::get('ce', $logInfo, '', true);
        $solutionInfo['re']     = Arr::get('re', $logInfo, '', true);
        $solutionInfo['detail'] = Arr::get('detail', $logInfo, '', true);

        return $solutionInfo;
    }

    public static function rejudge($id) {

        $solutionInfo = self::getById($id);
        if (empty($solutionInfo)) {
            throw new InterfaceException('solution不存在！');
        }

        $trans = new Trans(DbConfig::$SERVER_TRANS);
        $trans->begin();

        $solutionModel = new OjSolutionModel($trans);
        $solutionModel->updateById($id, array(
            'time_cost' => 0,
            'memory_cost'   => 0,
        ));
        $affects = self::updateResult($id, StatusVars::REJUDGE, $trans);

        // 调用HQOJ重判接口
        if ($solutionInfo['remote'] == StatusVars::REMOTE_HQU) {
            OjJudgeInterface::rejudge(array(
                'trans' => $trans,
                'id'    => $solutionInfo['run_id']
            ));
        }

        $trans->commit();

        return $affects;
    }

    private static function updateResult($id, $result, $trans = null) {

        $solutionInfo = self::getById($id);
        if (empty($solutionInfo)) {
            throw new InterfaceException('solution不存在！');
        }

        $innerTrans = $trans;
        if (null == $trans) {
            $innerTrans = new Trans(DbConfig::$SERVER_TRANS);
            $innerTrans->begin();
        }

        $solutionModel = new OjSolutionModel($innerTrans);

        $remote    = $solutionInfo['remote'];
        $globalId  = $solutionInfo['problem_global_id'];
        $userId    = $solutionInfo['user_id'];
        $contestId = $solutionInfo['contest_id'];

        // 如果状态从 非AC -> AC，那么修改user表和problem表，竞赛状态下不改变
        if (0 == $contestId && $result == StatusVars::ACCEPTED && $solutionInfo['result'] != StatusVars::ACCEPTED) {
            // 判断是否没有AC过
            $where = array(
                array('problem_global_id', '=', $globalId),
                array('user_id', '=', $userId),
                array('result', '=', StatusVars::ACCEPTED),
                array('contest_id', '=', 0),
            );
            $count = self::getCount($where);
            if (0 == $count) {
                $userInfo = UserCommonInterface::getById(array('id' => $userId));
                $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
                $remoteStr = strtolower(StatusVars::$REMOTE_SCHOOL[$remote]);
                UserCommonInterface::save(array(
                    'trans'                 => $innerTrans,
                    'id'                    => $userId,
                    'solved_all'            => $userInfo['solved_all']+1,
                    "solved_{$remoteStr}"   => $userInfo["solved_{$remoteStr}"]+1,
                ));
                OjProblemInterface::save(array(
                    'trans'     => $innerTrans,
                    'id'        => $globalId,
                    'solved'    => $problemInfo['solved']+1,
                ));
            }
        }

        // 如果状态从 AC -> 非AC，那么修改user表和problem表，竞赛状态下不改变
        if (0 == $contestId && $result != StatusVars::ACCEPTED && $solutionInfo['result'] == StatusVars::ACCEPTED) {
            // 判断是否只AC过一次
            $where = array(
                array('problem_global_id', '=', $globalId),
                array('user_id', '=', $userId),
                array('result', '=', StatusVars::ACCEPTED),
                array('contest_id', '=', 0),
            );
            $count = self::getCount($where);
            if (1 == $count) {
                $userInfo = UserCommonInterface::getById(array('id' => $userId));
                $problemInfo = OjProblemInterface::getById(array('id' => $globalId));
                $remoteStr = strtolower(StatusVars::$REMOTE_SCHOOL[$remote]);
                UserCommonInterface::save(array(
                    'trans'                 => $innerTrans,
                    'id'                    => $userId,
                    'solved_all'            => $userInfo['solved_all']-1,
                    "solved_{$remoteStr}"   => $userInfo["solved_{$remoteStr}"]-1,
                ));
                OjProblemInterface::save(array(
                    'trans'     => $innerTrans,
                    'id'        => $globalId,
                    'solved'    => $problemInfo['solved']-1,
                ));
            }
        }

        // 更新solution
        $affects = $solutionModel->updateById($id, array('result' => $result));
        if (null == $trans && null != $innerTrans) {  // 没有外部事务，并且存在内部事务
            $innerTrans->commit();
        }
        return $affects;
    }
}
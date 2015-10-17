<?php

/**
 * Class SqlBuilder 方便创建单张表操作的sql语句
 */

class SqlBuilder {

    // 不需要引号的数据类型
    private static $NO_GLUE_TYPES = array(
        'TINYINT',     // 8
        'SMALLINT',    // 16
        'MEDIUMINT',   // 24
        'INT',         // 32
        'BIGINT',      // 64
        'DECIMAL',
    );

    /**
     * @var mysqli  防止sql注入
     */
    private $mysqli     = null;

    // 主要属性
    private $dbName     = '';
    private $tableName  = '';
    private $fieldTypes = array();

    public function __construct($mysqli, $dbName, $tableName, $fieldTypes) {

        $this->mysqli       = $mysqli;
        $this->dbName       = $dbName;
        $this->tableName    = $tableName;
        $this->fieldTypes   = $fieldTypes;
    }

    private function getGlue($field) {
        if (!array_key_exists($field, $this->fieldTypes)) {
            throw new LibraryException("字段不存在！{$field}");
        }
        $type   = $this->fieldTypes[$field];
        return in_array(strtoupper($type), self::$NO_GLUE_TYPES) ? '' : "'";
    }

    private function checkValue($field, $value) {
        if (!array_key_exists($field, $this->fieldTypes)) {
            throw new LibraryException("字段不存在！{$field}");
        }
        if ('' === $this->getGlue($field)) {
            if (!is_numeric($value)) {
                throw new LibraryException('字段值类型错误，必须是数值类型！');
            }
        } else {
            if (!is_numeric($value) && !is_string($value)) {
                throw new LibraryException('字段值类型错误，必须是数值或者字符串类型！');
            }
        }
    }

    private function updateDataPart($data) {

        $ret = array();
        foreach ($data as $field => $value) {
            if (is_int($field) && is_string($value)) {
                // 如果下标是数字
                $ret[] = $value;
                continue;
            }
            $this->checkValue($field, $value);
            $glue = $this->getGlue($field);
            $ret[] = $field . '=' . $glue . $this->mysqli->real_escape_string($value) . $glue;
        }
        return ' SET ' . implode(',', $ret);
    }

    private function insertDataPart($dataList) {

        // 统一批量处理
        if (!isset($dataList[0])) {
            $data = $dataList;
            unset($dataList);
            $dataList[] = $data;
        }
        // 获取需要插入的字段
        $fieldStr = '('.implode(',', array_keys($dataList[0])).')';
        $valueList = array();
        foreach ($dataList as $data) {
            $dataSet = array();
            foreach ($data as $field => $value) {
                $this->checkValue($field, $value);
                $glue = $this->getGlue($field);
                $dataSet[] = $glue . $this->mysqli->real_escape_string($value) . $glue;
            }
            $valueList[] = '('.implode(',', array_values($dataSet)).')';
        }
        $valueStr = implode(', ', $valueList);
        return " {$fieldStr} VALUES {$valueStr}";
    }

    private function dfsWhere($where, $relation = ' AND ') {

        if (!is_array($where) || empty($where)) {
            throw new LibraryException('参数错误：$where');
        }

        $ret = array();
        foreach ($where as $filter) {

            if (!is_array($filter) || empty($filter)) {
                throw new LibraryException('参数错误：$where子条件错误');
            }

            if (array_key_exists('OR', $filter) || array_key_exists('or', $filter)) {
                // 或关系
                $orRet = array_key_exists('OR', $filter) ?
                    $this->dfsWhere($filter['OR'], ' OR ') :
                    $this->dfsWhere($filter['or'], ' OR ');
                $ret[] = "({$orRet})";
            } else if (is_array($filter[0])) {
                // 且关系
                $andRet = $this->dfsWhere($filter);
                $ret[] = $andRet;
            } else if (is_string($filter[0]) && count($filter) == 3) {
                // 三元原子条件
                $field  = $filter[0];
                $op     = strtoupper($filter[1]);
                $value  = $filter[2];
                $glue   = $this->getGlue($field);
                if ($op == 'IN' || $op == 'NOT IN') {

                    if (!is_array($value)) {
                        throw new LibraryException('参数错误：in条件必须是数组');
                    }

                    if (empty($value)) {
                        $ret[] = $op == 'IN' ? 'FALSE' : 'TRUE';
                        continue;
                    }
                    $tmpArr = array();
                    foreach ($value as $val) {
                        // 对每个值校验
                        $this->checkValue($field, $val);
                        $tmpArr[] = $glue . $this->mysqli->real_escape_string($val) . $glue;
                    }
                    $tmpArr = implode(',', $tmpArr);
                    $ret[] = "{$field} {$op} ({$tmpArr})";
                } else {
                    $this->checkValue($field, $value);
                    $val = $glue . $this->mysqli->real_escape_string($value) . $glue;
                    $ret[] = "{$field} {$op} {$val}";
                }
            } else if (is_string($filter[0]) && count($filter) == 1) {
                $ret[] = $filter[0];
            } else {
                throw new LibraryException('转换WHERE条件失败！');
            }
        }
        return implode($relation, $ret);
    }

    /**
     * @param   array   $where
     * @return  string  WHERE ... 或者 ''
     * @throws  LibraryException
     */
    private function getWherePart($where) {

        if (empty($where)) {
            return '';
        }

        // 分离出GROUP BY和HAVING
        $groupBy = '';
        $having = '';
        if (array_key_exists('group_by', $where)) {
            if (!array_key_exists($where['group_by'], $this->fieldTypes)) {
                throw new LibraryException('SqlBuilder只支持对一个字段GROUPBY！');
            }
            $groupBy = " GROUP BY {$where['group_by']}";
            if (isset($where['having'])) {
                $having = " HAVING {$where['having']}";
                unset($where['having']);
            }
            unset($where['group_by']);
        }

        if (empty($where)) {
            return "{$groupBy}{$having}";
        }

        // where条件转换为字符串
        $where = $this->dfsWhere($where);
        $ret = " WHERE {$where}{$groupBy}{$having}";
        return $ret;
    }

    private function getOrderPart($order) {

        if (empty($order)) {
            return '';
        }
        $retArr = array();
        foreach ($order as $field => $rule) {
            if (!array_key_exists($field, $this->fieldTypes)) {
                throw new LibraryException("字段不存在！{$field}");
            }
            $retArr[] = $field . ' ' . strtoupper($rule);
        }
        $ret = ' ORDER BY ' . implode(',', $retArr);
        return $ret;
    }

    private function getLimitPart($limit, $offset) {

        if ($limit === -1 && $offset > 0) {
            throw new LibraryException('构建LIMIT失败！');
        }
        if (-1 === $limit && 0 === $offset) {
            return '';
        }
        if ($offset > 0) return " LIMIT {$offset},{$limit}";
        return " LIMIT {$limit}";
    }

    public function createSelectSql($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {

        // 校验参数
        if (!is_string($field) || empty($field)) {
            throw new LibraryException('参数错误：$field');
        }
        if (!is_array($where)) {
            throw new LibraryException('参数错误：$where');
        }
        if (!is_array($order)) {
            throw new LibraryException('参数错误：$order');
        }
        if (!is_numeric($limit) || intval($limit) != $limit) {
            throw new LibraryException('参数错误：$limit');
        }
        if (!is_numeric($offset) || intval($offset) != $offset || $offset < 0) {
            throw new LibraryException('参数错误：$offset');
        }
        $where = $this->getWherePart($where);
        $order = $this->getOrderPart($order);
        $limit = $this->getLimitPart($limit, $offset);
        return "SELECT {$field} FROM `{$this->dbName}`.`{$this->tableName}`{$where}{$order}{$limit}";
    }

    public function createInsertSql($data) {

        if (!is_array($data) || empty($data)) {
            throw new LibraryException('参数错误：$data');
        }
        $data = $this->insertDataPart($data);
        return "INSERT INTO `{$this->dbName}`.`{$this->tableName}`{$data}";
    }

    public function createUpdateSql($data, $where) {

        if (!is_array($data)) {
            throw new LibraryException('参数错误：$data');
        }
        if (!is_array($where) || empty($where)) {
            throw new LibraryException('参数错误：$where');
        }
        if (empty($data)) {
            return 0;
        }
        $data  = $this->updateDataPart($data);
        $where = $this->getWherePart($where);
        return "UPDATE `{$this->dbName}`.`{$this->tableName}`{$data}{$where}";
    }

    public function createDeleteSql($where) {

        if (!is_array($where) || empty($where)) {
            throw new LibraryException('参数错误：$where');
        }
        $where = $this->getWherePart($where);
        return "DELETE FROM `{$this->dbName}`.`{$this->tableName}`{$where}";
    }
}
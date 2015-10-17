<?php

abstract class BaseModel {

    // [$dbName][$tableName][$field]['eq'|'in'][$value]作为key，保存这个表最近的getRowById和getRowByField的结果
    protected static $rowCache  = array();

    protected $isTransModel = false;

    /**
     * @var MysqliClient
     */
    protected $masterHandle = null;

    /**
     * @var MysqliClient
     */
    protected $slaveHandle  = null;

    /**
     * @var SqlBuilder
     */
    protected $sqlBuilder   = null;

    // 子类中初始化的属性
    protected $masterServer = array();
    protected $slaveServer  = array();
    protected $dbName       = '';
    protected $tableName    = '';
    protected $fieldTypes   = array();

    /**
     * @param   Trans|null   $handle 使用某个MysqliClient对象初始化Model
     *                              如果是null，那么Model会新建MysqliClient对象
     * @throws  LibraryException
     */
    protected function __construct(Trans $handle = null) {

        if (empty($this->dbName)) {
            throw new LibraryException('未指定默认数据库，请在Model或者DbConfig中指定数据库！');
        }

        if (!empty($handle)) {
            // 判断是否和主服务器匹配
            if ($this->masterServer['host'] != $handle->server['host']
            || $this->masterServer['port'] != $handle->server['port']) {
                throw new LibraryException('主机和端口和MYSQL主服务器不匹配！');
            }
            $this->isTransModel = true;
            $this->masterHandle = $handle;
            $this->slaveHandle  = $handle;
        } else {
            $this->masterHandle   = new MysqliClient($this->masterServer);
            $this->slaveHandle    = new MysqliClient($this->slaveServer);
        }
        $this->sqlBuilder   = new SqlBuilder($this->slaveHandle->mysqli, $this->dbName, $this->tableName, $this->fieldTypes);
    }

    final private function __clone() {}

    public function insert($data) {

        // $data必须有，如果要插入空数据，可以传入array('create_time' => time())
        if (!is_array($data) || empty($data)) {
            throw new LibraryException('参数错误：$data');
        }

        // 自动添加create_time, update_time，前提是这些字段在表中存在并且未传
        $now = time();
        if (array_key_exists('create_time', $this->fieldTypes) && !array_key_exists('create_time', $data)) {
            $data['create_time'] = $now;
        }
        if (array_key_exists('update_time', $this->fieldTypes) && !array_key_exists('update_time', $data)) {
            $data['update_time'] = $now;
        }

        // 插入，返回insert_id
        $sql = $this->sqlBuilder->createInsertSql($data);
        $id = $this->masterHandle->insert($sql);

        // 清除该表所有静态缓存
        unset(self::$rowCache[$this->dbName][$this->tableName]);
        return $id;
    }

    public function insertMulti($dataList) {

        if (!is_array($dataList) || empty($dataList)) {
            throw new LibraryException('参数错误：$dataList');
        }

        // 自动添加create_time, update_time，前提是这些字段在表中存在并且未传
        $now = time();
        foreach ($dataList as &$data) {
            if (array_key_exists('create_time', $this->fieldTypes) && !array_key_exists('create_time', $data)) {
                $data['create_time'] = $now;
            }
            if (array_key_exists('update_time', $this->fieldTypes) && !array_key_exists('update_time', $data)) {
                $data['update_time'] = $now;
            }

            // 无法插入空行
            if (empty($data)) {
                throw new LibraryException('数据库插入数据不能全部为空！');
            }
        }

        // 插入，返回true或者false
        $sql = $this->sqlBuilder->createInsertSql($dataList);
        $ret = $this->masterHandle->query($sql);

        // 清除该表所有静态缓存
        unset(self::$rowCache[$this->dbName][$this->tableName]);
        return $ret;
    }

    public function update($data, $where) {

        if (!is_array($data)) {
            throw new LibraryException('参数错误：$data');
        }
        if (!is_array($where) || empty($where)) {
            throw new LibraryException('参数错误：$where');
        }

        // 如果$data为空，说明不需要更新，affected_rows为0
        if (empty($data)) {
            return 0;
        }

        // 自动添加update_time，前提是字段在表中存在并且未传
        if (array_key_exists('update_time', $this->fieldTypes) && !array_key_exists('update_time', $data)) {
            $data['update_time'] = time();
        }

        // 更新
        $sql = $this->sqlBuilder->createUpdateSql($data, $where);
        $affects = $this->masterHandle->update($sql);

        // 清除该表所有静态缓存
        if (!empty($affects)) {
            unset(self::$rowCache[$this->dbName][$this->tableName]);
        }
        return $affects;
    }

    public function updateById($id, $data) {

        if (!is_numeric($id) || intval($id) != $id || $id <= 0) {
            throw new LibraryException('参数错误：$id');
        }
        if (!is_array($data)) {
            throw new LibraryException('参数错误：$data');
        }

        // 前提是id存在
        if (!array_key_exists('id', $this->fieldTypes)) {
            throw new LibraryException("数据表`{$this->dbName}`.`{$this->tableName}`或者Model中不存在字段id！");
        }

        if (empty($data)) {
            return 0;
        }

        // 更新
        $where = array(
            array('id', '=', $id),
        );
        $affects = self::update($data, $where);
        return $affects;
    }

    public function delete($where) {

        // where必须有，防止误删所有数据
        if (!is_array($where) || empty($where)) {
            throw new LibraryException('参数错误：$where');
        }

        // 物理删除
        $sql = $this->sqlBuilder->createDeleteSql($where);
        $affects = $this->masterHandle->delete($sql);

        // 如果删除成功，清空该表所有静态缓存
        if (!empty($affects)) {
            unset(self::$rowCache[$this->dbName][$this->tableName]);
        }
        return $affects;
    }

    public function deleteById($id) {

        if (!is_numeric($id) || intval($id) != $id || $id <= 0) {
            throw new LibraryException('参数错误：$id');
        }

        // 前提是id存在
        if (!array_key_exists('id', $this->fieldTypes)) {
            throw new LibraryException("数据表`{$this->dbName}`.`{$this->tableName}`或者Model中不存在字段id！");
        }

        // 根据id删除行
        $where = array(
            array('id', '=', $id),
        );
        $affects = self::delete($where);
        return $affects;
    }

    public function getList($field = '*', $where = array(), $order = array(), $limit = -1, $offset = 0) {

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

        // 默认id倒序
        if (empty($order) && array_key_exists('id', $this->fieldTypes)) {
            $order = array( 'id' => 'DESC' );
        }

        // 查询
        $sql = $this->sqlBuilder->createSelectSql($field, $where, $order, $limit, $offset);
        $retList = $this->slaveHandle->queryAll($sql);
        return $retList;
    }

    public function getCount($where = array()) {

        if (!is_array($where)) {
            throw new LibraryException('参数错误：$where');
        }

        $key = Arr::get('group_by', $where, '');

        if (!empty($key)) {
            // 如果是group_by，返回列表，array( key => count )
            $field = "{$key},count(1)";
            $list = $this->getList($field, $where);
            $ret = array();
            foreach ($list as $row) {
                $ret[$row[$key]] = $row['count(1)'];
            }
            return $ret;
        } else {
            // 返回一个数量
            $row = $this->getRow('count(1)', $where);
            return current($row);
        }
    }

    public function getRow($field = '*', $where = array(), $order = array(), $offset = 0) {

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
        if (!is_numeric($offset) || intval($offset) != $offset || $offset < 0) {
            throw new LibraryException('参数错误：$offset');
        }

        $sql = $this->sqlBuilder->createSelectSql($field, $where, $order, 1, $offset);
        $row = $this->slaveHandle->queryRow($sql);
        return $row;
    }

    /**
     * 如果id是一个数字，返回对应的一行；如果id是数组，返回一个关联列表；
     *
     * @param   int|array   $id
     * @return  array       null|一行, array()|关联列表
     * @throws  LibraryException
     */
    public function getById($id) {

        // 校验参数
        if (is_numeric($id) && intval($id) == $id && $id > 0
        || is_array($id)) {
            ;
        } else {
            throw new LibraryException('参数错误：$id');
        }

        if (empty($id)) {
            return array();
        }

        // 校验字段id是否存在
        if (!array_key_exists('id', $this->fieldTypes)) {
            throw new LibraryException("数据表`{$this->dbName}`.`{$this->tableName}`或者Model中不存在字段id！");
        }

        // 获取缓存，如果处在事务，那么不使用缓存
        $staticCache = array();
        if (!$this->isTransModel && isset(self::$rowCache[$this->dbName][$this->tableName]['id']['eq'])) {
            $staticCache = self::$rowCache[$this->dbName][$this->tableName]['id']['eq'];
        }

        if (is_array($id)) {

            $queryIds = $id;
            if (!empty($staticCache)) {
                // 如果缓存不为空，排除已经处在缓存中的id
                foreach ($queryIds as $i => $k) {
                    if (array_key_exists($k, $staticCache)) {
                        unset($queryIds[$i]);
                    }
                }
            }

            // 查询
            $retList = array();
            if (!empty($queryIds)) {
                $where = array();
                if (count($queryIds) == 1) {
                    $where[] = array('id', '=', current($queryIds));
                } else {
                    $where[] = array('id', 'IN', $queryIds);
                }
                $retList = $this->getList('*', $where);
                $retList = Arr::listToHash('id', $retList);
            }

            // 合并结果
            $ret = array();
            foreach ($id as $k) {
                $row = array();
                if (array_key_exists($k, $staticCache)) {
                    $row = $staticCache[$k];
                } else if (array_key_exists($k, $retList)) {
                    $row = $retList[$k];
                }

                // 不在事务中才能更新静态缓存
                if (!$this->isTransModel) {
                    self::$rowCache[$this->dbName][$this->tableName]['id']['eq'][$k] = $row;
                }

                $ret[$k] = $row;
            }
            return $ret;
        } else {

            // 在缓存中，返回
            if (array_key_exists($id, $staticCache)) {
                return $staticCache[$id];
            }

            // 查询
            $where = array(
                array('id', '=', $id),
            );
            $rowInfo = $this->getRow('*', $where);

            // 不在事务中才能更新静态缓存
            if (!$this->isTransModel) {
                self::$rowCache[$this->dbName][$this->tableName]['id']['eq'][$id] = $rowInfo;
            }
            return $rowInfo;
        }
    }

    /**
     * 如果value是一个值，返回对应的一行；如果value是数组，返回一个列表；
     *
     * @param   string          $field  表中的某个字段
     * @param   string|array    $value  筛选条件
     * @return  array           array()|一行|关联列表
     * @throws  LibraryException
     */
    public function getByField($field, $value) {

        // 校验
        if (!is_string($field) || empty($field)) {
            throw new LibraryException('参数错误：$field');
        }

        if (is_array($value) && empty($value)) {
            return array();
        }

        // 校验field字段
        if (!array_key_exists($field, $this->fieldTypes)) {
            throw new LibraryException("数据表`{$this->dbName}`.`{$this->tableName}`或者Model中不存在字段{$field}！");
        }

        // 获取缓存，如果处在事务，那么不使用缓存
        $staticCache = array();
        if (!$this->isTransModel) {
            if (is_array($value)) {
                if (isset(self::$rowCache[$this->dbName][$this->tableName][$field]['in'])) {
                    $staticCache = self::$rowCache[$this->dbName][$this->tableName][$field]['in'];
                }
            } else {
                if (isset(self::$rowCache[$this->dbName][$this->tableName][$field]['eq'])) {
                    $staticCache = self::$rowCache[$this->dbName][$this->tableName][$field]['eq'];
                }
            }
        }

        if (is_array($value)) {

            if (count($value) == 1) {

                $value = current($value);

                // 先从缓存中取
                if (array_key_exists($value, $staticCache)) {
                    return $staticCache[$value];
                }
                $where = array(
                    array($field, '=', $value),
                );
                $retList = $this->getList('*', $where);

                // 不在事务中才能更新静态缓存
                if (!$this->isTransModel) {
                    self::$rowCache[$this->dbName][$this->tableName][$field]['in'][$value] = $retList;
                }
                return $retList;

            } else {
                $where = array(
                    array($field, 'IN', $value),
                );
                $retList = $this->getList('*', $where);
                return $retList;
            }
        } else {

            // 在缓存中，返回
            if (array_key_exists($value, $staticCache)) {
                return $staticCache[$value];
            }

            // 查询
            $where = array(
                array($field, '=', $value),
            );
            $rowInfo = $this->getRow('*', $where);

            // 不在事务中才能更新静态缓存
            if (!$this->isTransModel) {
                self::$rowCache[$this->dbName][$this->tableName][$field][$value] = $rowInfo;
            }
            return $rowInfo;
        }
    }
}
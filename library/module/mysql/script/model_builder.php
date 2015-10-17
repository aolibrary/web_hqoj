<?php

require_once __DIR__ . '/../../../bootstrap.php';

class ModelBuilder {

    public $dbName      = '';
    public $tableName   = '';
    public $interface   = '';

    public $modelName   = '';
    public $mysqliClient    = null;

    public function __construct($dbName, $tableName, $interface) {

        $this->dbName       = $dbName;
        $this->tableName    = $tableName;
        $this->interface    = $interface;
        $this->mysqliClient = new MysqliClient(DbConfig::$SERVER_MASTER);

        $modelName = '';
        $arr = explode('_', $tableName);
        foreach ($arr as $str) {
            $modelName .= ucfirst($str);
        }
        $this->modelName = $modelName . 'Model';
    }

    public function getFieldTypes() {

        $sql         = "SHOW FULL COLUMNS FROM `{$this->dbName}`.`{$this->tableName}`";
        $typeList    = $this->mysqliClient->queryAll($sql);
        $maxFieldLen = 0;
        $maxTypeLen  = 0;
        foreach ($typeList as $typeInfo) {
            if (strlen($typeInfo['Field']) > $maxFieldLen) {
                $maxFieldLen = strlen($typeInfo['Field']);
            }
            $pos = strpos($typeInfo['Type'], '(');
            $type = false === $pos ? $typeInfo['Type'] : substr($typeInfo['Type'], 0, $pos);
            if (strlen($type) > $maxTypeLen) {
                $maxTypeLen = strlen($type);
            }
        }
        $str = '';
        foreach ($typeList as $typeInfo) {
            $field   = $typeInfo['Field'];
            $comment = $typeInfo['Comment'];
            $pos = strpos($typeInfo['Type'], '(');
            $type = false === $pos ? $typeInfo['Type'] : substr($typeInfo['Type'], 0, $pos);
            $type = strtolower($type);
            $blank1 = ' ';
            for ($i = 1; $i <= $maxFieldLen - strlen($field); $i++) {
                $blank1 .= ' ';
            }
            $blank2 = ' ';
            for ($i = 1; $i <= $maxTypeLen - strlen($type); $i++) {
                $blank2 .= ' ';
            }
            $str .= (!empty($str) ? "\n            " : '');
            $str .= "'{$field}'{$blank1}=> '{$type}',{$blank2}// {$comment}";
        }
        return $str;
    }

    public function getInterfaceFieldTypes() {

        $hash = array(
            'tinyint'    => 'TYPE_INT',
            'int'        => 'TYPE_INT',
            'bigint'     => 'TYPE_INT',
            'varchar'    => 'TYPE_STR',
            'text'       => 'TYPE_STR',
            'mediumtext' => 'TYPE_STR',
        );

        $sql         = "SHOW FULL COLUMNS FROM `{$this->dbName}`.`{$this->tableName}`";
        $typeList    = $this->mysqliClient->queryAll($sql);
        $maxFieldLen = 0;
        $maxTypeLen  = 0;
        foreach ($typeList as $typeInfo) {
            if (strlen($typeInfo['Field']) > $maxFieldLen) {
                $maxFieldLen = strlen($typeInfo['Field']);
            }
            $pos = strpos($typeInfo['Type'], '(');
            $type = false === $pos ? $typeInfo['Type'] : substr($typeInfo['Type'], 0, $pos);
            if (strlen($type) > $maxTypeLen) {
                $maxTypeLen = strlen($type);
            }
        }
        $str = '';
        foreach ($typeList as $typeInfo) {
            $field   = $typeInfo['Field'];
            $comment = $typeInfo['Comment'];
            $pos = strpos($typeInfo['Type'], '(');
            $type = false === $pos ? $typeInfo['Type'] : substr($typeInfo['Type'], 0, $pos);
            $type = strtolower($type);
            $type = $hash[$type];
            $blank1 = ' ';
            for ($i = 1; $i <= $maxFieldLen - strlen($field); $i++) {
                $blank1 .= ' ';
            }
            $blank2 = ' ';
            for ($i = 1; $i <= $maxTypeLen - strlen($type); $i++) {
                $blank2 .= ' ';
            }
            $str .= (!empty($str) ? "\n                " : '');
            $str .= "'{$field}'{$blank1}=> {$type},{$blank2}// {$comment}";
        }
        return $str;
    }

    public function createModel() {

        $src  = __DIR__ . '/TplModel.class.php';
        $dest = __DIR__ . '/' . $this->modelName . '.class.php';
        $typeStr = $this->getFieldTypes();
        $str = file_get_contents($src);
        $str = str_replace('TplModel', $this->modelName, $str);
        $str = str_replace('{$tableName}', $this->tableName, $str);
        $str = str_replace('\'{$fieldTypes}\'', $typeStr, $str);
        file_put_contents($dest, $str);
    }

    public function createLogic() {

        $src  = __DIR__ . '/TplLogic.class.php';
        $dest = __DIR__ . '/' . $this->interface . 'Logic.class.php';
        $str  = file_get_contents($src);
        $str  = str_replace('TplModel', $this->modelName, $str);
        $str  = str_replace('TplLogic', $this->interface.'Logic', $str);
        file_put_contents($dest, $str);
    }

    public function createInterface() {

        $src  = __DIR__ . '/TplInterface.class.php';
        $dest = __DIR__ . '/' . $this->interface . 'Interface.class.php';
        $typeStr = $this->getInterfaceFieldTypes();
        $str = file_get_contents($src);
        $str = str_replace('TplLogic', $this->interface.'Logic', $str);
        $str = str_replace('TplInterface', $this->interface.'Interface', $str);
        $str = str_replace('\'{$fieldTypes}\'', $typeStr, $str);
        file_put_contents($dest, $str);
    }

}

$dbName     = 'hqoj';
$tableName  = 'oj_solution';
$interface  = 'OjSolution';

$obj = new ModelBuilder($dbName, $tableName, $interface);
$obj->createModel();
if (!empty($interface)) {
    $obj->createLogic();
    $obj->createInterface();
}
echo "done\n";

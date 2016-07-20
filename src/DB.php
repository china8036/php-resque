<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 * 简易的应用框架
 * 主要实现自动加载\错误处理\配置读取
 */

namespace core;

class DB
{

    /**
     * 数据库链接
     * @var type 
     */
    private static $db;

    /**
     *
     * @var type 
     */
    protected $table;

    public function __construct($table)
    {
        if (!isset(self::$db)) {
            $this->initDB();
        }
        $this->table = $table;
    }

    /**
     * 初始化DB
     * @return \Slim\PDO\Database
     */
    public function initDB()
    {
        $db = Core::c('DB');
        self::$db = new \Slim\PDO\Database($db['dsn'], $db['usr'], $db['pwd']);
    }

    /**
     * 查询
     * @param type $column
     * @param type $operator
     * @param type $value
     * @param type $field
     * @return type
     */
    public function select($column, $operator, $value, $field = '*')
    {
        $selectStatement = self::$db->select($field)
                ->from($this->table)
                ->where($column, $operator, $value);
        $stmt = $selectStatement->execute();
        return $stmt->fetch();
    }

    /**
     * 插入数据
     * @param type $data
     * @return type
     */
    public function insert($data)
    {
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $values[] = $value;
        }
        $insertStatement = self::$db->insert($columns)
                ->into($this->table)
                ->values($values);

        return $insertStatement->execute(false);
    }

    /**
     * 更新数据
     * @param type $column
     * @param type $operator
     * @param type $value
     * @param type $data
     * @return type
     */
    public function update($column, $operator, $value, $data)
    {
        $updateStatement = self::$db->update($data)
                ->table($this->table)
                ->where($column, $operator, $value);

        return $updateStatement->execute();
    }

}

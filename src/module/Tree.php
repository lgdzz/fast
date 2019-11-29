<?php

namespace lgdz\module;

class Tree
{
    protected static $config = [
        'primary_key'  => 'id',
        'parent_key'   => 'parent_id',
        //展开属性
        'expanded_key' => 'expanded',
        //叶子节点属性
        'leaf_key'     => 'leaf',
        //子节点
        'children_key' => 'children',
        //展开子节点
        'expanded'     => false
    ];

    protected static $result = [];
    protected static $level = [];

    public static function makeTree($data, $options = [])
    {
        $dataset = self::buildData($data, $options);
        $result  = self::makeTreeCore(0, $dataset, 'normal');
        return $result;
    }

    public static function makeTreeForHtml($data, $options = [])
    {

        $dataset = self::buildData($data, $options);
        $result  = self::makeTreeCore(0, $dataset, 'linear');
        return $result;
    }

    private static function buildData($data, $options)
    {
        $config = array_merge(self::$config, $options);

        self::$config = $config;

        $result = [];
        foreach ($data as $row) {
            $id  = $row[$config['primary_key']];
            $pid = $row[$config['parent_key']];

            $result[$pid][$id] = $row;
        }

        return $result;
    }

    private static function makeTreeCore($index, $data, $type = 'linear')
    {
        $result = [];
        foreach ($data[$index] as $id => $row) {
            switch ($type) {
                case 'normal':
                    if (isset($data[$id])) {
                        $row[self::$config['expanded_key']] = self::$config['expanded'];
                        $row[self::$config['children_key']] = self::makeTreeCore($id, $data, $type);
                    } else {
                        $row[self::$config['leaf_key']] = true;
                    }
                    $result[] = $row;
                    break;
                case 'linear':
                    $parent_id        = $row[self::$config['parent_key']];
                    self::$level[$id] = $index == 0 ? 0 : self::$level[$parent_id] + 1;
                    $row['level']     = self::$level[$id];
                    self::$result[]   = $row;
                    if (isset($data[$id])) {
                        self::makeTreeCore($id, $data, $type);
                    }

                    $result = self::$result;
                    break;

            }
        }
        return $result;
    }
}
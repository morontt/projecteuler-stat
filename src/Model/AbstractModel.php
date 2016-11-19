<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 07.06.16
 * Time: 0:13
 */

namespace MttProjecteuler\Model;

abstract class AbstractModel
{
    /**
     * @var array
     */
    public static $fields = [];

    /**
     * @param array $data
     */
    abstract public function populate(array $data);

    /**
     * @return mixed
     */
    abstract public function toArray();

    /**
     * @return string
     */
    public static function getFieldsQueryString()
    {
        return implode(', ', array_map(
            function ($field) {
                return sprintf('`%s`', $field);
            },
            static::$fields
        ));
    }
}

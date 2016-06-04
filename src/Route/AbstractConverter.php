<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 21:40
 */

namespace MttProjecteuler\Route;

use MttProjecteuler\Database\Repository;

abstract class AbstractConverter
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $id
     * @return mixed
     */
    public abstract function convert($id);
}

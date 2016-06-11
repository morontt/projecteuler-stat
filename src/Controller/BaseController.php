<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 11.06.16
 * Time: 11:52
 */

namespace MttProjecteuler\Controller;


class BaseController
{
    /**
     * @param int $page
     * @param int $count
     * @param callable $urlGenerator
     * @return array
     */
    public function getPaginationMetadata($page, $count, callable $urlGenerator)
    {
        return [
            'current' => $page,
            'last' => $count,
            'previous' => ($page > 1),
            'next' => ($page < $count),
            'url_generator' => $urlGenerator,
        ];
    }
}

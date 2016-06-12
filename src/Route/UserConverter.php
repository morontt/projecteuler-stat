<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 12.06.16
 * Time: 20:25
 */

namespace MttProjecteuler\Route;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConverter extends AbstractConverter
{
    /**
     * @param string $slug
     * @return \MttProjecteuler\Model\User|null
     */
    public function convert($slug)
    {
        $entity = $this->repository->findUserBySlug($slug);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf('User %s does not exist', $slug));
        }

        return $entity;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 06.06.16
 * Time: 23:31
 */

namespace MttProjecteuler\Route;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LangConverter extends AbstractConverter
{
    /**
     * @param $id
     *
     * @return \MttProjecteuler\Model\Lang|null
     */
    public function convert($id)
    {
        $entity = $this->repository->findLang((int)$id);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf('Lang %d does not exist', $id));
        }

        return $entity;
    }
}

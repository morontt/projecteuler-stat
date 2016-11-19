<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 21:35
 */

namespace MttProjecteuler\Route;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SolutionConverter extends AbstractConverter
{
    /**
     * @param $id
     *
     * @return \MttProjecteuler\Model\Solution|null
     */
    public function convert($id)
    {
        $entity = $this->repository->findSolution((int)$id);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf('Solution %d does not exist', $id));
        }

        return $entity;
    }
}

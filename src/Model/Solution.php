<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 18:58
 */

namespace MttProjecteuler\Model;

use Carbon\Carbon;

class Solution
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $problemNumber;

    /**
     * @var int
     */
    protected $langId;

    /**
     * @var double
     */
    protected $executionTime;

    /**
     * @var double
     */
    protected $deviationTime;

    /**
     * @var Carbon
     */
    protected $completed;

    /**
     * @var int
     */
    protected $createdBy;

    /**
     * @var Carbon
     */
    protected $createdAt;

    /**
     * @var Carbon
     */
    protected $updatedAt;


    public function __construct()
    {
        $this->createdAt = new Carbon();
        $this->updatedAt = $this->createdAt;
    }

    /**
     * @param array $data
     */
    public function populate(array $data)
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }

        $this
            ->setProblemNumber($data['problem_number'])
            ->setLangId($data['lang_id'])
            ->setExecutionTime($data['execution_time'])
            ->setDeviationTime($data['deviation_time'])
            ->setCompleted(Carbon::createFromFormat('Y-m-d H:i:s', $data['completed']))
            ->setCreatedBy($data['created_by'])
            ->setCreatedAt(Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at']))
            ->setUpdatedAt(Carbon::createFromFormat('Y-m-d H:i:s', $data['updated_at']))
        ;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'problem_number' => $this->getProblemNumber(),
            'lang_id' => $this->getLangId(),
            'execution_time' => $this->getExecutionTime(),
            'deviation_time' => $this->getDeviationTime(),
            'completed' => $this->getCompleted()->format('Y-m-d H:i:s'),
            'created_by' => $this->getCreatedBy(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * @return int
     */
    public function getProblemNumber()
    {
        return $this->problemNumber;
    }

    /**
     * @param int $problemNumber
     * @return $this
     */
    public function setProblemNumber($problemNumber)
    {
        $this->problemNumber = (int)$problemNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * @param int $langId
     * @return $this
     */
    public function setLangId($langId)
    {
        $this->langId = $langId ? (int)$langId : null;

        return $this;
    }

    /**
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * @param float $executionTime
     * @return $this
     */
    public function setExecutionTime($executionTime)
    {
        $this->executionTime = (double)$executionTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getDeviationTime()
    {
        return $this->deviationTime;
    }

    /**
     * @param float $deviationTime
     * @return $this
     */
    public function setDeviationTime($deviationTime)
    {
        $this->deviationTime = (double)$deviationTime;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param \DateTime $completed
     * @return $this
     */
    public function setCompleted(\DateTime $completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param int $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = (int)$createdBy;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param Carbon $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

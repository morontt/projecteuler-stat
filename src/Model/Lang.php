<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 06.06.16
 * Time: 23:24
 */

namespace MttProjecteuler\Model;

use Carbon\Carbon;

class Lang extends AbstractModel
{
    /**
     * @var array
     */
    public static $fields = [
        'id',
        'name',
        'comment',
        'created_by',
        'created_at',
        'updated_at',
    ];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $comment;

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
            ->setName($data['name'])
            ->setComment($data['comment'])
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
            'name' => $this->getName(),
            'comment' => $this->getComment(),
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

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
        $this->createdBy = $createdBy;

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

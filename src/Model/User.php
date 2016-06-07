<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 04.06.16
 * Time: 11:41
 */

namespace MttProjecteuler\Model;

use Carbon\Carbon;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends AbstractModel implements UserInterface
{
    /**
     * @var array
     */
    public static $fields = [
        'id',
        'username',
        'email',
        'salt',
        'password_hash',
        'created_at',
    ];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Carbon
     */
    protected $createdAt;


    public function __construct()
    {
        $this->salt = substr(base_convert(bin2hex(openssl_random_pseudo_bytes(14)), 16, 36), 0, 20);
        $this->createdAt = new Carbon();
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
            ->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setSalt($data['salt'])
            ->setPassword($data['password_hash'])
            ->setCreatedAt(Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at']))
        ;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'salt' => $this->getSalt(),
            'password_hash' => $this->getPassword(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
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
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

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
    public function setCreatedAt(Carbon $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }
}

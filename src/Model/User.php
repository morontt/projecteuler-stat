<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 04.06.16
 * Time: 11:41
 */

namespace MttProjecteuler\Model;

use Carbon\Carbon;
use MttProjecteuler\Utils\Salt;
use MttProjecteuler\Utils\Slug;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends AbstractModel implements UserInterface, \Serializable
{
    /**
     * @var array
     */
    public static $fields = [
        'id',
        'username',
        'email',
        'email_hash',
        'slug',
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
    protected $slug;

    /**
     * @var string
     */
    protected $emailHash;

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
        $this->salt = Salt::generate();
        $this->createdAt = new Carbon();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->salt) = unserialize($serialized);
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
            ->setEmailHash($data['email_hash'])
            ->setSalt($data['salt'])
            ->setSlug($data['slug'])
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
            'email_hash' => $this->getEmailHash(),
            'salt' => $this->getSalt(),
            'slug' => $this->getSlug() ?: Slug::slugify($this->getUsername()),
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
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
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
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     *
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
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
     *
     * @return $this
     */
    public function setCreatedAt(Carbon $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailHash()
    {
        return $this->emailHash;
    }

    /**
     * @param string $emailHash
     *
     * @return $this
     */
    public function setEmailHash($emailHash)
    {
        $this->emailHash = $emailHash;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}

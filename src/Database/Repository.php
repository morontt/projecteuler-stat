<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 04.06.16
 * Time: 13:03
 */

namespace MttProjecteuler\Database;

use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use MttProjecteuler\Model\User;

class Repository
{
    /**
     * @var Connection
     */
    protected $db;


    /**
     * @param Connection $db
     */
    function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername($username)
    {
        $sql = "SELECT id, username, salt, password_hash, created_at FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('username', $username);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result) {
            $user = new User();
            $user
                ->setId($result['id'])
                ->setUsername($result['username'])
                ->setSalt($result['salt'])
                ->setPassword($result['password_hash'])
                ->setCreatedAt(Carbon::createFromFormat('Y-m-d H:i:s', $result['created_at']))
            ;
        } else {
            $user = null;
        }

        return $user;
    }
}

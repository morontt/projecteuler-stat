<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 04.06.16
 * Time: 13:03
 */

namespace MttProjecteuler\Database;

use Doctrine\DBAL\Connection;
use MttProjecteuler\Model\Solution;
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
        $stmt->bindValue('username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result) {
            $entity = new User();
            $entity->populate($result);
        } else {
            $entity = null;
        }

        return $entity;
    }

    /**
     * @param int $id
     * @return Solution|null
     */
    public function findSolution($id)
    {
        $sql = "SELECT id, problem_number, lang_id, execution_time, deviation_time, completed, created_by, created_at, updated_at FROM solutions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result) {
            $entity = new Solution();
            $entity->populate($result);
        } else {
            $entity = null;
        }

        return $entity;
    }

    /**
     * @return Solution[]
     */
    public function findAllSolutions()
    {
        $sql = <<<SQL
SELECT id, problem_number, lang_id, execution_time, deviation_time, completed, created_by, created_at, updated_at
FROM solutions ORDER BY id DESC
SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return array_map(
            function (array $data) {
                $entity = new Solution();
                $entity->populate($data);

                return $entity;
            },
            $results
        );
    }
}

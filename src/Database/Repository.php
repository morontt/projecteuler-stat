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
use MttProjecteuler\Model\Lang;
use MttProjecteuler\Model\Solution;
use MttProjecteuler\Model\User;

class Repository
{
    const LIMIT = 24;

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
        $fields = User::getFieldsQueryString();

        $sql = "SELECT {$fields} FROM `users` WHERE `username` = :username";
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
        $fields = Solution::getFieldsQueryString();

        $sql = "SELECT {$fields} FROM `solutions` WHERE `id` = :id";
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
        $fields = Solution::getFieldsQueryString();

        $sql = "SELECT {$fields} FROM `solutions` ORDER BY `id` DESC";
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

    /**
     * @param int $page
     * @return array
     */
    public function getResultsForStartpage($page)
    {
        $sql = <<<SQL
SELECT `s`.`id`, `s`.`problem_number`, `s`.`execution_time`, `s`.`deviation_time`, `s`.`created_at`, `u`.`username`,
  `u`.`email_hash`, `l`.`name` AS `lang_name`, `l`.`comment` AS `lang_comment`
FROM `solutions` AS `s`
INNER JOIN `users` AS `u` ON `s`.`created_by` = `u`.`id`
LEFT JOIN `languages` AS `l` ON `s`.`lang_id` = `l`.`id`
ORDER BY `s`.`id` DESC LIMIT :limit OFFSET :offset
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', self::LIMIT, \PDO::PARAM_INT);
        $stmt->bindValue('offset', ($page - 1) * self::LIMIT, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return array_map(
            function (array $data) {
                $data['created'] = Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at']);

                return $data;
            },
            $results
        );
    }

    /**
     * @param int $id
     * @return Lang|null
     */
    public function findLang($id)
    {
        $fields = Lang::getFieldsQueryString();

        $sql = "SELECT {$fields} FROM `languages` WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result) {
            $entity = new Lang();
            $entity->populate($result);
        } else {
            $entity = null;
        }

        return $entity;
    }

    /**
     * @return Lang[]
     */
    public function findAllLanguages()
    {
        $fields = Lang::getFieldsQueryString();

        $sql = "SELECT {$fields} FROM `languages` ORDER BY `id` DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return array_map(
            function (array $data) {
                $entity = new Lang();
                $entity->populate($data);

                return $entity;
            },
            $results
        );
    }

    /**
     * @return array
     */
    public function getLanguageChoices()
    {
        $fields = Lang::getFieldsQueryString();

        $sql = "SELECT {$fields} FROM `languages` ORDER BY `name` ASC, `comment` ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $choices = [];
        foreach ($results as $item) {
            $choices[sprintf('%s (%s)', $item['name'], $item['comment'])] = $item['id'];
        }

        return $choices;
    }
}

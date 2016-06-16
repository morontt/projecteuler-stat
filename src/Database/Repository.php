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
     * @param string $slug
     * @return User|null
     */
    public function findUserBySlug($slug)
    {
        $fields = User::getFieldsQueryString();

        $sql = "SELECT {$fields} FROM `users` WHERE `slug` = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('slug', $slug, \PDO::PARAM_STR);
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
        $sql = $this->getCommonResultsQuery();
        $sql .= ' WHERE `public` = 1 ORDER BY `s`.`id` DESC LIMIT :limit OFFSET :offset';

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
     * @param User $user
     * @param int $page
     * @return array
     */
    public function getResultsForUser(User $user, $page)
    {
        $sql = $this->getCommonResultsQuery();
        $sql .= ' WHERE `public` = 1 AND `u`.`id` = :userid ORDER BY `s`.`id` DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('userid', $user->getId(), \PDO::PARAM_INT);
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
     * @param int $number
     * @return array
     */
    public function getResultsByProblem($number)
    {
        $sql = $this->getCommonResultsQuery();
        $sql .= ' WHERE `public` = 1 AND `s`.`problem_number` = :number ORDER BY `s`.`execution_time`';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('number', $number, \PDO::PARAM_INT);
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
     * @return array
     */
    public function getSingleResult($id)
    {
        $sql = <<<SQL
SELECT `s`.`id`, `s`.`problem_number`, `s`.`execution_time`, `s`.`deviation_time`, `s`.`created_at`, `u`.`username`,
  `u`.`email_hash`, `l`.`name` AS `lang_name`, `l`.`comment` AS `lang_comment`, `u`.`slug` AS `user_slug`,
  `s`.`source_html`, `s`.`updated_at`
FROM `solutions` AS `s`
INNER JOIN `users` AS `u` ON `s`.`created_by` = `u`.`id`
LEFT JOIN `languages` AS `l` ON `s`.`lang_id` = `l`.`id`
WHERE `s`.`id` = :id AND `public` = 1
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if (is_array($result)) {
            $result['created'] = Carbon::createFromFormat('Y-m-d H:i:s', $result['created_at']);
            $result['updated'] = Carbon::createFromFormat('Y-m-d H:i:s', $result['updated_at']);
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getCountResultsForStartpage()
    {
        $stmt = $this->db->prepare('SELECT COUNT(`id`) AS `cnt` FROM `solutions` WHERE `public` = 1');
        $stmt->execute();
        $result = $stmt->fetch();

        return (int)ceil((int)$result['cnt'] / self::LIMIT);
    }

    /**
     * @param User $user
     * @return int
     */
    public function getCountResultsForUser(User $user)
    {
        $sql = <<<SQL
SELECT COUNT(`s`.`id`) AS `cnt` FROM `solutions` AS `s`
INNER JOIN `users` AS `u` ON `s`.`created_by` = `u`.`id`
WHERE `u`.`id` = :userid AND `public` = 1
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('userid', $user->getId(), \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        return (int)ceil((int)$result['cnt'] / self::LIMIT);
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
            $key = $item['comment'] ? sprintf('%s (%s)', $item['name'], $item['comment']) : $item['name'];
            $choices[$key] = $item['id'];
        }

        return $choices;
    }

    /**
     * @param int $number
     * @return array
     */
    public function findProblem($number)
    {
        $sql = "SELECT `id`, `problem_number`, `title` FROM `problems` WHERE `problem_number` = :number";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('number', $number, \PDO::PARAM_INT);
        $stmt->execute();

        $problem = $stmt->fetch();

        if (!$problem) {
            $curl = curl_init('https://projecteuler.net/problem=' . $number);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0');
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($curl);

            if (curl_errno($curl)) {
                $problem = ['title' => 'undefined'];
            } else {
                curl_close($curl);

                $matches = [];
                if (preg_match('/<h2>([^<]+)<\/h2>/', $data, $matches)) {
                    $problem = [
                        'title' => $matches[1],
                        'problem_number' => $number,
                        'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('problems', $problem);
                } else {
                    $problem = ['title' => 'undefined'];
                }
            }
        }

        return $problem;
    }

    /**
     * @return Carbon
     */
    public function lastModifiedStartpage()
    {
        $stmt = $this->db->prepare(
            "SELECT `updated_at` FROM `solutions` WHERE `public` = 1 ORDER BY `updated_at` DESC LIMIT 1"
        );
        $stmt->execute();

        $col = $stmt->fetch(\PDO::FETCH_COLUMN);
        if ($col) {
            $col = '2016-06-16 22:45:45';
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $col);
    }

    /**
     * @param User $user
     * @return Carbon
     */
    public function lastModifiedByUser(User $user)
    {
        $stmt = $this->db->prepare(
            "SELECT `updated_at` FROM `solutions`
               WHERE `public` = 1 AND `created_by` = :id ORDER BY `updated_at` DESC LIMIT 1"
        );
        $stmt->bindValue('id', $user->getId(), \PDO::PARAM_INT);
        $stmt->execute();

        $col = $stmt->fetch(\PDO::FETCH_COLUMN);
        if ($col) {
            $col = '2016-06-16 22:45:45';
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $col);
    }

    /**
     * @param int $number
     * @return Carbon
     */
    public function lastModifiedByProblem($number)
    {
        $stmt = $this->db->prepare(
            "SELECT `updated_at` FROM `solutions`
               WHERE `public` = 1 AND `problem_number` = :problem_number ORDER BY `updated_at` DESC LIMIT 1"
        );
        $stmt->bindValue('problem_number', $number, \PDO::PARAM_INT);
        $stmt->execute();

        $col = $stmt->fetch(\PDO::FETCH_COLUMN);
        if ($col) {
            $col = '2016-06-16 22:45:45';
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $col);
    }

    /**
     * @return string
     */
    protected function getCommonResultsQuery()
    {
        return <<<SQL
SELECT `s`.`id`, `s`.`problem_number`, `s`.`execution_time`, `s`.`deviation_time`, `s`.`created_at`, `u`.`username`,
  `u`.`email_hash`, `l`.`name` AS `lang_name`, `l`.`comment` AS `lang_comment`, `u`.`slug` AS `user_slug`
FROM `solutions` AS `s`
INNER JOIN `users` AS `u` ON `s`.`created_by` = `u`.`id`
LEFT JOIN `languages` AS `l` ON `s`.`lang_id` = `l`.`id`
SQL;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 02.06.16
 * Time: 0:07
 */

namespace MttProjecteuler\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class Migrator
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
     * @param bool $dryRun
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function migrate($dryRun)
    {
        $sm = $this->db->getSchemaManager();

        $fromSchema = $sm->createSchema();
        $toSchema = $this->buildSchema();

        $queries = $fromSchema->getMigrateToSql($toSchema, $this->db->getDatabasePlatform());
        if (!$dryRun) {
            foreach ($queries as $query) {
                $this->db->exec($query);
            }
        }

        return $queries;
    }

    /**
     * @return Schema
     */
    protected function buildSchema()
    {
        $schema = new Schema();

        $userTable = $schema->createTable('users');
        $userTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true,]);
        $userTable->addColumn('username', 'string', ['length' => 32,]);
        $userTable->addColumn('salt', 'string', ['length' => 20,]);
        $userTable->addColumn('password_hash', 'string', ['length' => 64]);
        $userTable->addColumn('created_at', 'datetime');
        $userTable->setPrimaryKey(['id']);
        $userTable->addUniqueIndex(['username']);

        $languagesTable = $schema->createTable('languages');
        $languagesTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true,]);
        $languagesTable->addColumn('name', 'string', ['length' => 32]);
        $languagesTable->addColumn('comment', 'string', ['length' => 255]);
        $languagesTable->addColumn('created_at', 'datetime');
        $languagesTable->setPrimaryKey(['id']);

        $solutionsTable = $schema->createTable('solutions');
        $solutionsTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true,]);
        $solutionsTable->addColumn('problem_number', 'integer');
        $solutionsTable->addColumn('lang_id', 'integer', ['notnull' => false, 'unsigned' => true,]);
        $solutionsTable->addColumn('execution_time', 'float');
        $solutionsTable->addColumn('deviation_time', 'float');
        $solutionsTable->addColumn('completed', 'datetime');
        $solutionsTable->addColumn('created_at', 'datetime');
        $solutionsTable->setPrimaryKey(['id']);

        $solutionsTable->addForeignKeyConstraint($languagesTable, ['lang_id'], ['id'], ['onDelete' => 'SET NULL']);

        return $schema;
    }
}

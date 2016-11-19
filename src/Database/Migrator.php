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
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @param bool $dryRun
     *
     * @return array
     *
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
        $userTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $userTable->addColumn('username', 'string', ['length' => 32]);
        $userTable->addColumn('slug', 'string', ['length' => 32]);
        $userTable->addColumn('email', 'string', ['length' => 64]);
        $userTable->addColumn('email_hash', 'string', ['length' => 32]);
        $userTable->addColumn('salt', 'string', ['length' => 20]);
        $userTable->addColumn('password_hash', 'string', ['length' => 64]);
        $userTable->addColumn('created_at', 'datetime');
        $userTable->setPrimaryKey(['id']);
        $userTable->addUniqueIndex(['username']);
        $userTable->addUniqueIndex(['slug']);
        $userTable->addUniqueIndex(['email']);

        $languagesTable = $schema->createTable('languages');
        $languagesTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $languagesTable->addColumn('name', 'string', ['length' => 32]);
        $languagesTable->addColumn('slug', 'string', ['length' => 32]);
        $languagesTable->addColumn('comment', 'string', ['notnull' => false, 'length' => 255]);
        $languagesTable->addColumn('lexer', 'string', ['notnull' => false, 'length' => 16]);
        $languagesTable->addColumn('created_by', 'integer', ['unsigned' => true]);
        $languagesTable->addColumn('created_at', 'datetime');
        $languagesTable->addColumn('updated_at', 'datetime');
        $languagesTable->addIndex(['slug']);
        $languagesTable->setPrimaryKey(['id']);

        $languagesTable->addForeignKeyConstraint($userTable, ['created_by'], ['id'], ['onDelete' => 'CASCADE']);

        $solutionsTable = $schema->createTable('solutions');
        $solutionsTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $solutionsTable->addColumn('problem_number', 'integer');
        $solutionsTable->addColumn('lang_id', 'integer', ['notnull' => false, 'unsigned' => true]);
        $solutionsTable->addColumn('source_link', 'string', ['notnull' => false, 'length' => 255]);
        $solutionsTable->addColumn('source_html', 'text', ['notnull' => false, 'length' => 100000]);
        $solutionsTable->addColumn('execution_time', 'float');
        $solutionsTable->addColumn('deviation_time', 'float', ['notnull' => false]);
        $solutionsTable->addColumn('public', 'smallint');
        $solutionsTable->addColumn('created_by', 'integer', ['unsigned' => true]);
        $solutionsTable->addColumn('created_at', 'datetime');
        $solutionsTable->addColumn('updated_at', 'datetime');
        $solutionsTable->setPrimaryKey(['id']);

        $solutionsTable->addForeignKeyConstraint($languagesTable, ['lang_id'], ['id'], ['onDelete' => 'SET NULL']);
        $solutionsTable->addForeignKeyConstraint($userTable, ['created_by'], ['id'], ['onDelete' => 'CASCADE']);

        $problemTable = $schema->createTable('problems');
        $problemTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $problemTable->addColumn('problem_number', 'integer');
        $problemTable->addColumn('title', 'string', ['length' => 255]);
        $problemTable->addColumn('created_at', 'datetime');
        $problemTable->addUniqueIndex(['problem_number']);
        $problemTable->setPrimaryKey(['id']);

        return $schema;
    }
}

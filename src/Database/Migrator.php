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

        $languagesTable = $schema->createTable('lang');
        $languagesTable->addColumn('id', 'integer', ['autoincrement' => true,]);
        $languagesTable->addColumn('name', 'string', ['length' => 32]);
        $languagesTable->addColumn('comment', 'string', ['length' => 255]);
        $languagesTable->addUniqueIndex(['name']);
        $languagesTable->setPrimaryKey(['id']);

        $solutionsTable = $schema->createTable('solution');
        $solutionsTable->addColumn('id', 'integer', ['autoincrement' => true,]);
        $solutionsTable->addColumn('problem_number', 'integer');
        $solutionsTable->addColumn('lang_id', 'integer', ['notnull' => false,]);
        $solutionsTable->addColumn('execution_time', 'float');
        $solutionsTable->addColumn('deviation_time', 'float');
        $solutionsTable->addColumn('completed', 'datetime');
        $solutionsTable->addUniqueIndex(['problem_number']);
        $solutionsTable->setPrimaryKey(['id']);

        $solutionsTable->addForeignKeyConstraint($languagesTable, ['lang_id'], ['id'], ['onDelete' => 'SET NULL']);

        return $schema;
    }
}

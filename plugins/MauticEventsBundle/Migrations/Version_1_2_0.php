<?php

declare(strict_types=1);

namespace MauticPlugin\MauticEventsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

class Version_1_2_0 extends AbstractMauticMigration
{
    protected const TABLE_NAME = 'events';

    protected function mysqlUp(Schema $schema): void
    {
        $tableName = $this->getPrefixedTableName(self::TABLE_NAME);
        
        if (!$schema->hasTable($tableName)) {
            return;
        }

        $table = $schema->getTable($tableName);

        // Add new fields if they don't exist
        if (!$table->hasColumn('event_external_id')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN event_external_id VARCHAR(191) NOT NULL");
        }
        
        if (!$table->hasColumn('name')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN name VARCHAR(191) NOT NULL");
        }
        
        if (!$table->hasColumn('website')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN website VARCHAR(191) DEFAULT NULL");
        }
        
        if (!$table->hasColumn('currency')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN currency VARCHAR(10) DEFAULT NULL");
        }
        
        if (!$table->hasColumn('country')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN country VARCHAR(191) DEFAULT NULL");
        }
        
        if (!$table->hasColumn('city')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN city VARCHAR(191) DEFAULT NULL");
        }
        
        if (!$table->hasColumn('registration_url')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN registration_url VARCHAR(191) DEFAULT NULL");
        }
        
        if (!$table->hasColumn('suitecrm_id')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN suitecrm_id VARCHAR(191) DEFAULT NULL");
        }
        
        if (!$table->hasColumn('created_at')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN created_at DATETIME DEFAULT NULL");
        }
        
        if (!$table->hasColumn('updated_at')) {
            $this->addSql("ALTER TABLE $tableName ADD COLUMN updated_at DATETIME DEFAULT NULL");
        }

        // Add unique constraint on event_external_id
        $uniqueConstraintName = $this->generatePropertyName(self::TABLE_NAME, 'uniq', ['event_external_id']);
        $this->addSql("ALTER TABLE $tableName ADD UNIQUE INDEX $uniqueConstraintName (event_external_id)");
    }
}
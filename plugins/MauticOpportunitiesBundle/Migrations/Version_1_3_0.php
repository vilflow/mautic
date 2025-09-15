<?php

declare(strict_types=1);

namespace MauticPlugin\MauticOpportunitiesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

class Version_1_3_0 extends AbstractMauticMigration
{
    protected const TABLE_NAME = 'opportunities';

    protected function mysqlUp(Schema $schema): void
    {
        $tableName = $this->getPrefixedTableName();
        if ($schema->hasTable($tableName)) {
            return;
        }

        $contactsTable = $this->getPrefixedTableName('leads');
        $eventsTable   = $this->getPrefixedTableName('events');

        $this->addSql("CREATE TABLE $tableName (
            id INT AUTO_INCREMENT NOT NULL,
            contact_id BIGINT UNSIGNED DEFAULT NULL,
            event_id INT UNSIGNED DEFAULT NULL,
            opportunity_external_id VARCHAR(255) NOT NULL,
            stage VARCHAR(50) DEFAULT NULL,
            amount NUMERIC(10, 2) DEFAULT NULL,
            abstract_review_result_url VARCHAR(255) DEFAULT NULL,
            invoice_url VARCHAR(255) DEFAULT NULL,
            invitation_url VARCHAR(255) DEFAULT NULL,
            suitecrm_id VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,
            name VARCHAR(255) DEFAULT NULL,
            UNIQUE INDEX UNIQ_406D4DB09D3A4B1E (opportunity_external_id),
            INDEX IDX_406D4DB0E7A1254A (contact_id),
            INDEX IDX_406D4DB071F7E88B (event_id),
            INDEX IDX_406D4DB0B88E6C90 (suitecrm_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        
        $this->addSql("ALTER TABLE $tableName ADD CONSTRAINT FK_406D4DB0E7A1254A FOREIGN KEY (contact_id) REFERENCES $contactsTable (id)");
        $this->addSql("ALTER TABLE $tableName ADD CONSTRAINT FK_406D4DB071F7E88B FOREIGN KEY (event_id) REFERENCES $eventsTable (id)");
    }

    protected function mysqlDown(Schema $schema): void
    {
        $tableName = $this->getPrefixedTableName();
        if ($schema->hasTable($tableName)) {
            $this->addSql("DROP TABLE $tableName");
        }
    }
}
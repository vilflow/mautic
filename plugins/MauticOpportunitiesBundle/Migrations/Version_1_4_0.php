<?php

declare(strict_types=1);

namespace MauticPlugin\MauticOpportunitiesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

class Version_1_4_0 extends AbstractMauticMigration
{
    protected function mysqlUp(Schema $schema): void
    {
        $opportunityContactsTable = $this->getPrefixedTableName('opportunity_contacts');
        
        // Drop the opportunity_contacts table since we now use contact_id directly in opportunities table
        if ($schema->hasTable($opportunityContactsTable)) {
            $this->addSql("DROP TABLE $opportunityContactsTable");
        }
    }

    protected function mysqlDown(Schema $schema): void
    {
        // Recreation of opportunity_contacts table if rollback is needed
        $tableName = $this->getPrefixedTableName('opportunity_contacts');
        if (!$schema->hasTable($tableName)) {
            $opportunitiesTable = $this->getPrefixedTableName('opportunities');
            $contactsTable      = $this->getPrefixedTableName('leads');

            $this->addSql("CREATE TABLE $tableName (
                id INT AUTO_INCREMENT NOT NULL,
                opportunity_id INT DEFAULT NULL,
                contact_id BIGINT UNSIGNED DEFAULT NULL,
                dateAdded DATETIME DEFAULT NULL,
                dateModified DATETIME DEFAULT NULL,
                UNIQUE INDEX UNIQ_3E3A5F9BA7B57E0B3DBB69 (opportunity_id, contact_id),
                INDEX IDX_3E3A5F9BA7B57E0B (opportunity_id),
                INDEX IDX_3E3A5F9BE7A1254A (contact_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
            
            $this->addSql("ALTER TABLE $tableName ADD CONSTRAINT FK_3E3A5F9BA7B57E0B FOREIGN KEY (opportunity_id) REFERENCES $opportunitiesTable (id) ON DELETE CASCADE");
            $this->addSql("ALTER TABLE $tableName ADD CONSTRAINT FK_3E3A5F9BE7A1254A FOREIGN KEY (contact_id) REFERENCES $contactsTable (id)");
        }
    }
}
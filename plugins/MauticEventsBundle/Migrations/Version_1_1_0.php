<?php

declare(strict_types=1);

namespace MauticPlugin\MauticEventsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

class Version_1_1_0 extends AbstractMauticMigration
{
    protected const TABLE_NAME = 'event_contacts';

    protected function mysqlUp(Schema $schema): void
    {
        $tableName = $this->getPrefixedTableName();
        if ($schema->hasTable($tableName)) {
            return;
        }

        [$eventIdx, $eventFk]     = $this->generateKeys(self::TABLE_NAME, ['event_id']);
        [$contactIdx, $contactFk] = $this->generateKeys(self::TABLE_NAME, ['contact_id']);
        $uniq                     = $this->generatePropertyName(self::TABLE_NAME, 'uniq', ['event_id', 'contact_id']);

        $eventsTable  = $this->getPrefixedTableName('events');
        $contactsTable = $this->getPrefixedTableName('leads');

        $this->addSql("CREATE TABLE $tableName (
            id INT AUTO_INCREMENT NOT NULL,
            event_id INT NOT NULL,
            contact_id INT NOT NULL,
            date_added DATETIME DEFAULT NULL,
            date_modified DATETIME DEFAULT NULL,
            INDEX $eventIdx (event_id),
            INDEX $contactIdx (contact_id),
            UNIQUE INDEX $uniq (event_id, contact_id),
            CONSTRAINT $eventFk FOREIGN KEY (event_id) REFERENCES $eventsTable (id) ON DELETE CASCADE,
            CONSTRAINT $contactFk FOREIGN KEY (contact_id) REFERENCES $contactsTable (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }
}

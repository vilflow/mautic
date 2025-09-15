<?php

declare(strict_types=1);

namespace MauticPlugin\MauticOpportunitiesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

class Version_1_5_0 extends AbstractMauticMigration
{
    protected function mysqlUp(Schema $schema): void
    {
        $tableName = $this->getPrefixedTableName('opportunities');

        if ($schema->hasTable($tableName)) {
            $table = $schema->getTable($tableName);

            // Add new standard opportunity fields
            if (!$table->hasColumn('date_entered')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN date_entered DATETIME DEFAULT NULL");
            }

            if (!$table->hasColumn('date_modified')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN date_modified DATETIME DEFAULT NULL");
            }

            if (!$table->hasColumn('description')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN description LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('deleted')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN deleted TINYINT(1) DEFAULT 0");
            }

            if (!$table->hasColumn('opportunity_type')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN opportunity_type VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('lead_source')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN lead_source VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('amount_usdollar')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN amount_usdollar DECIMAL(26,6) DEFAULT NULL");
            }

            if (!$table->hasColumn('date_closed')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN date_closed DATE DEFAULT NULL");
            }

            if (!$table->hasColumn('next_step')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN next_step VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('sales_stage')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN sales_stage VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('probability')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN probability INT DEFAULT NULL");
            }

            // Add custom fields with _c suffix
            if (!$table->hasColumn('institution_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN institution_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('review_result_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN review_result_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_book_send_date_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_book_send_date_c DATE DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_review_result_url_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_review_result_url_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_book_dpublication_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_book_dpublication_c TINYINT(1) DEFAULT 0");
            }

            if (!$table->hasColumn('extra_paper_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN extra_paper_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('sales_receipt_url_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN sales_receipt_url_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_result_send_date_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_result_send_date_c DATE DEFAULT NULL");
            }

            if (!$table->hasColumn('registration_type_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN registration_type_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_book_information_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_book_information_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('payment_status_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN payment_status_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('coupon_code_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN coupon_code_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_result_ready_date_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_result_ready_date_c DATE DEFAULT NULL");
            }

            if (!$table->hasColumn('paper_title_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN paper_title_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('sms_permission_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN sms_permission_c TINYINT(1) DEFAULT 0");
            }

            if (!$table->hasColumn('jjwg_maps_geocode_status_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN jjwg_maps_geocode_status_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('invoice_url_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN invoice_url_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('presentation_type_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN presentation_type_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('invitation_letter_url_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN invitation_letter_url_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('withdraw_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN withdraw_c TINYINT(1) DEFAULT 0");
            }

            if (!$table->hasColumn('keywords_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN keywords_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('jjwg_maps_lng_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN jjwg_maps_lng_c DOUBLE PRECISION DEFAULT NULL");
            }

            if (!$table->hasColumn('jjwg_maps_lat_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN jjwg_maps_lat_c DOUBLE PRECISION DEFAULT NULL");
            }

            if (!$table->hasColumn('transaction_id_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN transaction_id_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('co_authors_names_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN co_authors_names_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('abstract_attachment_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN abstract_attachment_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('acceptance_letter_url_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN acceptance_letter_url_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('payment_channel_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN payment_channel_c VARCHAR(255) DEFAULT NULL");
            }

            if (!$table->hasColumn('wire_transfer_attachment_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN wire_transfer_attachment_c LONGTEXT DEFAULT NULL");
            }

            if (!$table->hasColumn('jjwg_maps_address_c')) {
                $this->addSql("ALTER TABLE $tableName ADD COLUMN jjwg_maps_address_c VARCHAR(255) DEFAULT NULL");
            }

            // Update existing records to set date_entered and date_modified from created_at and updated_at
            $this->addSql("UPDATE $tableName SET date_entered = created_at WHERE date_entered IS NULL AND created_at IS NOT NULL");
            $this->addSql("UPDATE $tableName SET date_modified = updated_at WHERE date_modified IS NULL AND updated_at IS NOT NULL");

            // Set default values for new records
            $this->addSql("UPDATE $tableName SET date_entered = NOW() WHERE date_entered IS NULL");
            $this->addSql("UPDATE $tableName SET date_modified = NOW() WHERE date_modified IS NULL");

            // Update amount precision if needed
            $this->addSql("ALTER TABLE $tableName MODIFY amount DECIMAL(19,6) DEFAULT NULL");
        }
    }

    protected function mysqlDown(Schema $schema): void
    {
        $tableName = $this->getPrefixedTableName('opportunities');

        if ($schema->hasTable($tableName)) {
            // Remove all the added columns
            $this->addSql("ALTER TABLE $tableName DROP COLUMN date_entered");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN date_modified");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN description");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN deleted");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN opportunity_type");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN lead_source");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN amount_usdollar");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN date_closed");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN next_step");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN sales_stage");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN probability");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN institution_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN review_result_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_book_send_date_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_review_result_url_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_book_dpublication_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN extra_paper_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN sales_receipt_url_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_result_send_date_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN registration_type_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_book_information_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN payment_status_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN coupon_code_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_result_ready_date_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN paper_title_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN sms_permission_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN jjwg_maps_geocode_status_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN invoice_url_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN presentation_type_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN invitation_letter_url_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN withdraw_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN keywords_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN jjwg_maps_lng_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN jjwg_maps_lat_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN transaction_id_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN co_authors_names_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN abstract_attachment_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN acceptance_letter_url_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN payment_channel_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN wire_transfer_attachment_c");
            $this->addSql("ALTER TABLE $tableName DROP COLUMN jjwg_maps_address_c");

            // Revert amount precision
            $this->addSql("ALTER TABLE $tableName MODIFY amount DECIMAL(10,2) DEFAULT NULL");
        }
    }
}
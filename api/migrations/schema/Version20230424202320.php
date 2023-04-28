<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230424202320 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE activity_progress_label
                            ADD CONSTRAINT camp_activity_progress_label_unique
                                UNIQUE  (campid, position) 
                                    DEFERRABLE INITIALLY DEFERRED');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE activity_progress_label DROP CONSTRAINT camp_activity_progress_label');
    }
}

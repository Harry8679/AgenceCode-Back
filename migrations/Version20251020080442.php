<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020080442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tariff ADD price_cents_before_credit INT UNSIGNED NOT NULL, ADD price_cents_after_credit INT UNSIGNED NOT NULL, DROP price_cent, CHANGE duration_minutes duration_minutes INT UNSIGNED NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_tariff_combo ON tariff (subject_id, class_level, duration_minutes)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_tariff_combo ON tariff');
        $this->addSql('ALTER TABLE tariff ADD price_cent INT NOT NULL, DROP price_cents_before_credit, DROP price_cents_after_credit, CHANGE duration_minutes duration_minutes INT NOT NULL');
    }
}

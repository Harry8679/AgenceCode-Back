<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251031103335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupon ADD unit_price_parent_cents INT UNSIGNED NOT NULL, ADD unit_price_teacher_cents INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE tariff ADD teacher_rate_cents INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupon DROP unit_price_parent_cents, DROP unit_price_teacher_cents');
        $this->addSql('ALTER TABLE tariff DROP teacher_rate_cents');
    }
}

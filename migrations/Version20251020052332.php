<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020052332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coupon (id INT AUTO_INCREMENT NOT NULL, child_id INT DEFAULT NULL, subject_id INT NOT NULL, code VARCHAR(16) NOT NULL, class_level VARCHAR(255) NOT NULL, duration_minutes INT NOT NULL, remaining_minutes INT NOT NULL, status VARCHAR(255) NOT NULL, purchased_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_used_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_64BF3F02DD62C21B (child_id), INDEX IDX_64BF3F0223EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coupon_usage (id INT AUTO_INCREMENT NOT NULL, coupon_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, minutes_used INT NOT NULL, lesson_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3EA5018066C5951B (coupon_id), INDEX IDX_3EA5018041807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tariff (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, class_level VARCHAR(255) NOT NULL, price_cent INT NOT NULL, is_active TINYINT(1) NOT NULL, duration_minutes INT NOT NULL, INDEX IDX_9465207D23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02DD62C21B FOREIGN KEY (child_id) REFERENCES child (id)');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F0223EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE coupon_usage ADD CONSTRAINT FK_3EA5018066C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id)');
        $this->addSql('ALTER TABLE coupon_usage ADD CONSTRAINT FK_3EA5018041807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tariff ADD CONSTRAINT FK_9465207D23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F02DD62C21B');
        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F0223EDC87');
        $this->addSql('ALTER TABLE coupon_usage DROP FOREIGN KEY FK_3EA5018066C5951B');
        $this->addSql('ALTER TABLE coupon_usage DROP FOREIGN KEY FK_3EA5018041807E1D');
        $this->addSql('ALTER TABLE tariff DROP FOREIGN KEY FK_9465207D23EDC87');
        $this->addSql('DROP TABLE coupon');
        $this->addSql('DROP TABLE coupon_usage');
        $this->addSql('DROP TABLE tariff');
    }
}

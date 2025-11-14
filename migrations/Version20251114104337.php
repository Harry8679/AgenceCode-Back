<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114104337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_assignment (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, child_id INT NOT NULL, subject_id INT NOT NULL, status VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BADAE61A41807E1D (teacher_id), INDEX IDX_BADAE61ADD62C21B (child_id), INDEX IDX_BADAE61A23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_assignment ADD CONSTRAINT FK_BADAE61A41807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE teacher_assignment ADD CONSTRAINT FK_BADAE61ADD62C21B FOREIGN KEY (child_id) REFERENCES child (id)');
        $this->addSql('ALTER TABLE teacher_assignment ADD CONSTRAINT FK_BADAE61A23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher_assignment DROP FOREIGN KEY FK_BADAE61A41807E1D');
        $this->addSql('ALTER TABLE teacher_assignment DROP FOREIGN KEY FK_BADAE61ADD62C21B');
        $this->addSql('ALTER TABLE teacher_assignment DROP FOREIGN KEY FK_BADAE61A23EDC87');
        $this->addSql('DROP TABLE teacher_assignment');
    }
}

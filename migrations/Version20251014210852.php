<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014210852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE child_subject (child_id INT NOT NULL, subject_id INT NOT NULL, INDEX IDX_997A7661DD62C21B (child_id), INDEX IDX_997A766123EDC87 (subject_id), PRIMARY KEY(child_id, subject_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE child_subject ADD CONSTRAINT FK_997A7661DD62C21B FOREIGN KEY (child_id) REFERENCES child (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE child_subject ADD CONSTRAINT FK_997A766123EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B3542994AF957A');
        $this->addSql('DROP INDEX IDX_22B3542994AF957A ON child');
        $this->addSql('ALTER TABLE child DROP subjects_id');
        $this->addSql('CREATE UNIQUE INDEX uniq_subject_name ON subject (name)');
        $this->addSql('CREATE UNIQUE INDEX uniq_subject_slug ON subject (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE child_subject DROP FOREIGN KEY FK_997A7661DD62C21B');
        $this->addSql('ALTER TABLE child_subject DROP FOREIGN KEY FK_997A766123EDC87');
        $this->addSql('DROP TABLE child_subject');
        $this->addSql('ALTER TABLE child ADD subjects_id INT NOT NULL');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B3542994AF957A FOREIGN KEY (subjects_id) REFERENCES subject (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_22B3542994AF957A ON child (subjects_id)');
        $this->addSql('DROP INDEX uniq_subject_name ON subject');
        $this->addSql('DROP INDEX uniq_subject_slug ON subject');
    }
}

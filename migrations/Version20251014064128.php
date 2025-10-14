<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014064128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE child ADD subjects_id INT NOT NULL, DROP subjects');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B3542994AF957A FOREIGN KEY (subjects_id) REFERENCES subject (id)');
        $this->addSql('CREATE INDEX IDX_22B3542994AF957A ON child (subjects_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B3542994AF957A');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP INDEX IDX_22B3542994AF957A ON child');
        $this->addSql('ALTER TABLE child ADD subjects JSON NOT NULL, DROP subjects_id');
    }
}

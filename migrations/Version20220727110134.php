<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220727110134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pdv_log (id INT AUTO_INCREMENT NOT NULL, pdv_id INT DEFAULT NULL, datas LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_77E3F9551069E8D (pdv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pdv_log ADD CONSTRAINT FK_77E3F9551069E8D FOREIGN KEY (pdv_id) REFERENCES pdv (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pdv_log');
    }
}

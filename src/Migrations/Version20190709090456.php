<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190709090456 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messages CHANGE conv_id conv_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photos CHANGE post_id post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avatars CHANGE user_id user_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE avatars CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE messages CHANGE conv_id conv_id INT NOT NULL');
        $this->addSql('ALTER TABLE photos CHANGE post_id post_id INT NOT NULL');
        $this->addSql('ALTER TABLE posts CHANGE user_id user_id INT NOT NULL');
    }
}

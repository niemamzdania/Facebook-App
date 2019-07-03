<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190703163812 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quests DROP FOREIGN KEY FK_quests_users');
        $this->addSql('ALTER TABLE quests CHANGE user_id user_id INT DEFAULT NULL, CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D34A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quests DROP FOREIGN KEY FK_989E5D34A76ED395');
        $this->addSql('ALTER TABLE quests CHANGE user_id user_id INT NOT NULL, CHANGE status status INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_quests_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}

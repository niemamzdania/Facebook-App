<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216101351 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quests ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D34166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX FK__quests_projects ON quests (project_id)');
        $this->addSql('ALTER TABLE quests RENAME INDEX fk_quests_users TO IDX_989E5D34A76ED395');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quests DROP FOREIGN KEY FK_989E5D34166D1F9C');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP INDEX FK__quests_projects ON quests');
        $this->addSql('ALTER TABLE quests DROP project_id');
        $this->addSql('ALTER TABLE quests RENAME INDEX idx_989e5d34a76ed395 TO FK_quests_users');
    }
}

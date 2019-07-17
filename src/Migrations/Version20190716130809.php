<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190716130809 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversations ADD sender INT DEFAULT NULL, ADD reciver INT DEFAULT NULL, DROP user1, DROP user2');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF15F004ACF FOREIGN KEY (sender) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1D0E3AE91 FOREIGN KEY (reciver) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX FK_conversations_users ON conversations (sender)');
        $this->addSql('CREATE INDEX FK_conversations_users_2 ON conversations (reciver)');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_users_posts');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE quests CHANGE user_id user_id INT DEFAULT NULL, CHANGE add_date add_date DATETIME NOT NULL, CHANGE end_date end_date DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF15F004ACF');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1D0E3AE91');
        $this->addSql('DROP INDEX FK_conversations_users ON conversations');
        $this->addSql('DROP INDEX FK_conversations_users_2 ON conversations');
        $this->addSql('ALTER TABLE conversations ADD user1 INT NOT NULL, ADD user2 INT NOT NULL, DROP sender, DROP reciver');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAA76ED395');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_users_posts FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quests CHANGE user_id user_id INT NOT NULL, CHANGE add_date add_date DATE NOT NULL, CHANGE end_date end_date DATE NOT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190716185219 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAA76ED395');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D94B89032C');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D94B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE quests DROP FOREIGN KEY FK_989E5D34A76ED395');
        $this->addSql('ALTER TABLE quests CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D34A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E962FC61EC7');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E962FC61EC7 FOREIGN KEY (conv_id) REFERENCES conversations (id)');
        $this->addSql('ALTER TABLE avatars DROP FOREIGN KEY FK_B0C98520A76ED395');
        $this->addSql('ALTER TABLE avatars ADD CONSTRAINT FK_B0C98520A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_conversations_users');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_conversations_users_2');
        $this->addSql('ALTER TABLE conversations CHANGE sender sender INT DEFAULT NULL, CHANGE reciver reciver INT DEFAULT NULL');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF15F004ACF FOREIGN KEY (sender) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1D0E3AE91 FOREIGN KEY (reciver) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE avatars DROP FOREIGN KEY FK_B0C98520A76ED395');
        $this->addSql('ALTER TABLE avatars ADD CONSTRAINT FK_B0C98520A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF15F004ACF');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1D0E3AE91');
        $this->addSql('ALTER TABLE conversations CHANGE sender sender INT NOT NULL, CHANGE reciver reciver INT NOT NULL');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_conversations_users FOREIGN KEY (sender) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_conversations_users_2 FOREIGN KEY (reciver) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E962FC61EC7');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E962FC61EC7 FOREIGN KEY (conv_id) REFERENCES conversations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D94B89032C');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D94B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAA76ED395');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quests DROP FOREIGN KEY FK_989E5D34A76ED395');
        $this->addSql('ALTER TABLE quests CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D34A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }
}

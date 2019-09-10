<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190906173450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E962FC61EC7');
        $this->addSql('DROP TABLE conversations');
        $this->addSql('DROP TABLE messages');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conversations (id INT AUTO_INCREMENT NOT NULL, sender INT DEFAULT NULL, reciver INT DEFAULT NULL, INDEX FK_conversations_users_2 (reciver), INDEX FK_conversations_users (sender), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, conv_id INT DEFAULT NULL, content VARCHAR(300) NOT NULL COLLATE utf8mb4_unicode_ci, date DATETIME NOT NULL, INDEX FK_conversations_messages (conv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF15F004ACF FOREIGN KEY (sender) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1D0E3AE91 FOREIGN KEY (reciver) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E962FC61EC7 FOREIGN KEY (conv_id) REFERENCES conversations (id) ON DELETE CASCADE');
    }
}

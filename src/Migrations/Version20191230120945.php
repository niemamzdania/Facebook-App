<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191230120945 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE photos (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX FK_posts_photos (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(50) NOT NULL, app_id VARCHAR(20) DEFAULT NULL, app_secret VARCHAR(40) DEFAULT NULL, page_id VARCHAR(20) DEFAULT NULL, user_access_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversations (id INT AUTO_INCREMENT NOT NULL, user_1 INT DEFAULT NULL, user_2 INT DEFAULT NULL, INDEX IDX_C2521BF1632F5D60 (user_1), INDEX IDX_C2521BF1FA260CDA (user_2), INDEX FK_users_conversations (user_1, user_2), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quests (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, project_id INT DEFAULT NULL, content VARCHAR(200) NOT NULL, status INT NOT NULL, add_date DATE NOT NULL, end_date DATE NOT NULL, INDEX IDX_989E5D34A76ED395 (user_id), INDEX FK__quests_projects (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avatars (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B0C98520A76ED395 (user_id), INDEX FK_users_avatars (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, INDEX FK_projects_users (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, conv_id INT DEFAULT NULL, sender INT DEFAULT NULL, recipient INT DEFAULT NULL, time DATETIME NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_DB021E965F004ACF (sender), INDEX IDX_DB021E966804FB49 (recipient), INDEX FK_conversations_messages (conv_id), INDEX FK_users_messages (sender, recipient), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, content VARCHAR(300) NOT NULL, date DATETIME NOT NULL, status TINYINT(1) NOT NULL, INDEX FK_users_posts (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D94B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1632F5D60 FOREIGN KEY (user_1) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1FA260CDA FOREIGN KEY (user_2) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D34A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quests ADD CONSTRAINT FK_989E5D34166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE avatars ADD CONSTRAINT FK_B0C98520A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E962FC61EC7 FOREIGN KEY (conv_id) REFERENCES conversations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E965F004ACF FOREIGN KEY (sender) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E966804FB49 FOREIGN KEY (recipient) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1632F5D60');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1FA260CDA');
        $this->addSql('ALTER TABLE quests DROP FOREIGN KEY FK_989E5D34A76ED395');
        $this->addSql('ALTER TABLE avatars DROP FOREIGN KEY FK_B0C98520A76ED395');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4A76ED395');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E965F004ACF');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E966804FB49');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAA76ED395');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E962FC61EC7');
        $this->addSql('ALTER TABLE quests DROP FOREIGN KEY FK_989E5D34166D1F9C');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D94B89032C');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE conversations');
        $this->addSql('DROP TABLE quests');
        $this->addSql('DROP TABLE avatars');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE posts');
    }
}

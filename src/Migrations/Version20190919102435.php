<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190919102435 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1632F5D60');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1FA260CDA');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1632F5D60 FOREIGN KEY (user_1) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1FA260CDA FOREIGN KEY (user_2) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E962FC61EC7');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E965F004ACF');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E966804FB49');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E962FC61EC7 FOREIGN KEY (conv_id) REFERENCES conversations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E965F004ACF FOREIGN KEY (sender) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E966804FB49 FOREIGN KEY (recipient) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1632F5D60');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1FA260CDA');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1632F5D60 FOREIGN KEY (user_1) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1FA260CDA FOREIGN KEY (user_2) REFERENCES users (id)');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E962FC61EC7');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E965F004ACF');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E966804FB49');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E962FC61EC7 FOREIGN KEY (conv_id) REFERENCES conversations (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E965F004ACF FOREIGN KEY (sender) REFERENCES users (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E966804FB49 FOREIGN KEY (recipient) REFERENCES users (id)');
    }
}

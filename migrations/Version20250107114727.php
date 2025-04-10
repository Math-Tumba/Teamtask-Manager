<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107114727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_friends_requests (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))');
        $this->addSql('CREATE INDEX IDX_EBAF7C5A3AD8644E ON user_friends_requests (user_source)');
        $this->addSql('CREATE INDEX IDX_EBAF7C5A233D34C1 ON user_friends_requests (user_target)');
        $this->addSql('CREATE TABLE user_friends (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))');
        $this->addSql('CREATE INDEX IDX_79E36E633AD8644E ON user_friends (user_source)');
        $this->addSql('CREATE INDEX IDX_79E36E63233D34C1 ON user_friends (user_target)');
        $this->addSql('ALTER TABLE user_friends_requests ADD CONSTRAINT FK_EBAF7C5A3AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_friends_requests ADD CONSTRAINT FK_EBAF7C5A233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E633AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_friends ADD CONSTRAINT FK_79E36E63233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_friends_requests DROP CONSTRAINT FK_EBAF7C5A3AD8644E');
        $this->addSql('ALTER TABLE user_friends_requests DROP CONSTRAINT FK_EBAF7C5A233D34C1');
        $this->addSql('ALTER TABLE user_friends DROP CONSTRAINT FK_79E36E633AD8644E');
        $this->addSql('ALTER TABLE user_friends DROP CONSTRAINT FK_79E36E63233D34C1');
        $this->addSql('DROP TABLE user_friends_requests');
        $this->addSql('DROP TABLE user_friends');
    }
}

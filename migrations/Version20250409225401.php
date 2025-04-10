<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250409225401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_friend_requests (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FEBFDC943AD8644E ON user_friend_requests (user_source)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FEBFDC94233D34C1 ON user_friend_requests (user_target)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests ADD CONSTRAINT FK_FEBFDC943AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests ADD CONSTRAINT FK_FEBFDC94233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends_requests DROP CONSTRAINT fk_ebaf7c5a3ad8644e
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends_requests DROP CONSTRAINT fk_ebaf7c5a233d34c1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_friends_requests
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_friends_requests (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_ebaf7c5a233d34c1 ON user_friends_requests (user_target)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_ebaf7c5a3ad8644e ON user_friends_requests (user_source)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends_requests ADD CONSTRAINT fk_ebaf7c5a3ad8644e FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends_requests ADD CONSTRAINT fk_ebaf7c5a233d34c1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests DROP CONSTRAINT FK_FEBFDC943AD8644E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests DROP CONSTRAINT FK_FEBFDC94233D34C1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_friend_requests
        SQL);
    }
}

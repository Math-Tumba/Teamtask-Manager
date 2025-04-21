<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416134600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_friend (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30BCB75C3AD8644E ON user_friend (user_source)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30BCB75C233D34C1 ON user_friend (user_target)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_friend_request (user_sender_id INT NOT NULL, user_receiver_id INT NOT NULL, PRIMARY KEY(user_sender_id, user_receiver_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_64AD92CDF6C43E79 ON user_friend_request (user_sender_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_64AD92CD64482423 ON user_friend_request (user_receiver_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend ADD CONSTRAINT FK_30BCB75C3AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend ADD CONSTRAINT FK_30BCB75C233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request ADD CONSTRAINT FK_64AD92CDF6C43E79 FOREIGN KEY (user_sender_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request ADD CONSTRAINT FK_64AD92CD64482423 FOREIGN KEY (user_receiver_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends DROP CONSTRAINT fk_79e36e633ad8644e
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends DROP CONSTRAINT fk_79e36e63233d34c1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests DROP CONSTRAINT fk_febfdc943ad8644e
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests DROP CONSTRAINT fk_febfdc94233d34c1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_friends
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_friend_requests
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_friends (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_79e36e63233d34c1 ON user_friends (user_target)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_79e36e633ad8644e ON user_friends (user_source)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_friend_requests (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_febfdc94233d34c1 ON user_friend_requests (user_target)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_febfdc943ad8644e ON user_friend_requests (user_source)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends ADD CONSTRAINT fk_79e36e633ad8644e FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friends ADD CONSTRAINT fk_79e36e63233d34c1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests ADD CONSTRAINT fk_febfdc943ad8644e FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_requests ADD CONSTRAINT fk_febfdc94233d34c1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend DROP CONSTRAINT FK_30BCB75C3AD8644E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend DROP CONSTRAINT FK_30BCB75C233D34C1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request DROP CONSTRAINT FK_64AD92CDF6C43E79
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request DROP CONSTRAINT FK_64AD92CD64482423
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_friend
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_friend_request
        SQL);
    }
}

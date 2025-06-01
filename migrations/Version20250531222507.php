<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250531222507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request ADD id INT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE user_friend_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            UPDATE user_friend_request SET id = nextval('user_friend_request_id_seq')
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request ALTER COLUMN id SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request DROP CONSTRAINT user_friend_request_pkey
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request ADD PRIMARY KEY (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP SEQUENCE user_friend_request_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request DROP CONSTRAINT user_friend_request_pkey
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request DROP id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend_request ADD PRIMARY KEY (user_sender_id, user_receiver_id)
        SQL);
    }
}

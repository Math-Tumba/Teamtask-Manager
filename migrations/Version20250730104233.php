<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250730104233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE "user_friendship_id_seq" INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user_friendship" (id INT NOT NULL, user1_id INT NOT NULL, user2_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D55362B356AE248B ON "user_friendship" (user1_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D55362B3441B8B65 ON "user_friendship" (user2_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_friendship" ADD CONSTRAINT FK_D55362B356AE248B FOREIGN KEY (user1_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_friendship" ADD CONSTRAINT FK_D55362B3441B8B65 FOREIGN KEY (user2_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        
        $this->addSql(<<<'SQL'
            INSERT INTO "user_friendship" (id, user1_id, user2_id) 
                SELECT nextval('user_friendship_id_seq'), user_source, user_target FROM "user_friend"
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend DROP CONSTRAINT fk_30bcb75c233d34c1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend DROP CONSTRAINT fk_30bcb75c3ad8644e
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_friend
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE "user_friendship_id_seq" CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_friend (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_30bcb75c233d34c1 ON user_friend (user_target)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_30bcb75c3ad8644e ON user_friend (user_source)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend ADD CONSTRAINT fk_30bcb75c233d34c1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_friend ADD CONSTRAINT fk_30bcb75c3ad8644e FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO "user_friend" (user_source, user_target) 
                SELECT user1_id, user2_id FROM "user_friendship"
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE "user_friendship" DROP CONSTRAINT FK_D55362B356AE248B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user_friendship" DROP CONSTRAINT FK_D55362B3441B8B65
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user_friendship"
        SQL);
    }
}

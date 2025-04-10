<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017084706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD website VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD github VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD twitter VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD instagram VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD facebook VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD linkedin VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP website');
        $this->addSql('ALTER TABLE "user" DROP github');
        $this->addSql('ALTER TABLE "user" DROP twitter');
        $this->addSql('ALTER TABLE "user" DROP instagram');
        $this->addSql('ALTER TABLE "user" DROP facebook');
        $this->addSql('ALTER TABLE "user" DROP linkedin');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210812105906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // PASSWORD admin
        $this->addSql('INSERT INTO user (email, roles, password) VALUES (\'admin@admin.com\', \'["ROLE_ADMIN"]\', \'$2y$13$WRQSFPqdVpXYjEOT.jyP7eZnp.4Qk4duPZnY6Rh1KKErJI74e/VZK\')');

        // PASSWORD user1
        $this->addSql('INSERT INTO user (email, roles, password) VALUES (\'user1@user.com\', \'[""]\' ,\'$2y$13$zP9W628CekLyUbX.hBogquHksIWbwbAUwVxExeAEopvtbP5gaUDbi\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
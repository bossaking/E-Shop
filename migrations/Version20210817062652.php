<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210817062652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart_position ADD cart_order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart_position ADD CONSTRAINT FK_D8B3AB9E13002253 FOREIGN KEY (cart_order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_D8B3AB9E13002253 ON cart_position (cart_order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_position DROP FOREIGN KEY FK_D8B3AB9E13002253');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP INDEX IDX_D8B3AB9E13002253 ON cart_position');
        $this->addSql('ALTER TABLE cart_position DROP cart_order_id');
    }
}

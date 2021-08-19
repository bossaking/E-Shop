<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210819092107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_position (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, cart_order_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_D8B3AB9EA76ED395 (user_id), INDEX IDX_D8B3AB9E4584665A (product_id), INDEX IDX_D8B3AB9E13002253 (cart_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, available TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, status_id INT NOT NULL, archived TINYINT(1) DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F52993986BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, available TINYINT(1) DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // PASSWORD admin
        $this->addSql('INSERT INTO user (email, roles, password) VALUES (\'admin@admin.com\', \'["ROLE_ADMIN"]\', \'$2y$13$WRQSFPqdVpXYjEOT.jyP7eZnp.4Qk4duPZnY6Rh1KKErJI74e/VZK\')');
        // PASSWORD user1
        $this->addSql('INSERT INTO user (email, roles, password) VALUES (\'user1@user.com\', \'[""]\' ,\'$2y$13$zP9W628CekLyUbX.hBogquHksIWbwbAUwVxExeAEopvtbP5gaUDbi\')');
        // PASSWORD user2
        $this->addSql('INSERT INTO user (email, roles, password) VALUES (\'user2@user.com\', \'[""]\' ,\'$2y$13$YqRHpWJcj3C2I1iIGHEY1uI5qW3xupa237BvBLvZ9JDufQYQR0As2\')');
        // PASSWORD user3
        $this->addSql('INSERT INTO user (email, roles, password) VALUES (\'user3@user.com\', \'[""]\' ,\'$2y$13$C3xnC7dhKjGltwWQ/ZQmpuvxz5Kfevpuj2E5Z38aQe9sp7E4rwROq\')');

        $this->addSql('INSERT INTO status (name) values (\'In progress\')');
        $this->addSql('INSERT INTO status (name) values (\'Received\')');
        $this->addSql('INSERT INTO status (name) values (\'Delivered\')');
        $this->addSql('INSERT INTO status (name) values (\'Canceled\')');

        $this->addSql('ALTER TABLE cart_position ADD CONSTRAINT FK_D8B3AB9EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart_position ADD CONSTRAINT FK_D8B3AB9E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE cart_position ADD CONSTRAINT FK_D8B3AB9E13002253 FOREIGN KEY (cart_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE cart_position DROP FOREIGN KEY FK_D8B3AB9E13002253');
        $this->addSql('ALTER TABLE cart_position DROP FOREIGN KEY FK_D8B3AB9E4584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986BF700BD');
        $this->addSql('ALTER TABLE cart_position DROP FOREIGN KEY FK_D8B3AB9EA76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP TABLE cart_position');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE user');
    }
}

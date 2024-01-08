<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240108135433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme_story (theme_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_B3748FDD59027487 (theme_id), INDEX IDX_B3748FDDAA5D4036 (story_id), PRIMARY KEY(theme_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE theme_story ADD CONSTRAINT FK_B3748FDD59027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE theme_story ADD CONSTRAINT FK_B3748FDDAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE theme_story DROP FOREIGN KEY FK_B3748FDD59027487');
        $this->addSql('ALTER TABLE theme_story DROP FOREIGN KEY FK_B3748FDDAA5D4036');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE theme_story');
    }
}

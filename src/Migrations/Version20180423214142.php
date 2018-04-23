<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180423214142 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql('ALTER TABLE lesson ADD start_date_time DATETIME NULL;');
    }

    public function down(Schema $schema)
    {
       $this->addSql('ALTER TABLE lesson DROP start_date_time;');
    }
}

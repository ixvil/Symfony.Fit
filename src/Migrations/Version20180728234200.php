<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 28/07/2018
 * Time: 23:42
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180728234200 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE lesson ADD overridden_users_limit int DEFAULT null  NULL;
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE lesson DROP overridden_users_limit;
        ");
    }
}
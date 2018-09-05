<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 09/08/2018
 * Time: 00:53
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180906000100 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("
            INSERT INTO `ticket_plan_type` (`id`, `name`) 
            VALUES (4, 'Безлимитные абонементы')
        ");
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
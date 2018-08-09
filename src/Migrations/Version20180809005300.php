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

class Version20180809005300 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("
            INSERT INTO `ticket_plan_type` (`id`, `name`) 
            VALUES (3, 'Персональные абонементы')
        ");
        $this->addSql("
            INSERT INTO `ticket_plan` (`id`, `type_id`, `lessons_count`, `days_to_outdated`, `price`, `name`) 
            VALUES (8, 3, 1, 365, 1500, 'Персональная тренировка')
        ");
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 22/07/2018
 * Time: 00:00
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180722000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("update ticket_plan_type set name = 'Абонементы, доступные для покупки' where id = 1");
        $this->addSql("insert into ticket_plan_type (id, name) VALUES (2, 'ПОдждарочные абонементы')");
        $this->addSql("UPDATE ticket_plan set name = 'Абонемент на 8 занятий' where id = 1;");
        $this->addSql("UPDATE ticket_plan set name = 'Подарочное занятие' where id = 2;");
        $this->addSql("UPDATE ticket_plan set type_id = 2 where id = 2;");
        $this->addSql("UPDATE ticket_plan set name = 'Разовое занятие' where id = 3;");
        $this->addSql("UPDATE ticket_plan set name = 'Подарочный абонемент на 4 занятия' where id = 4;");
        $this->addSql("UPDATE ticket_plan set type_id = 2 where id = 4;");
        $this->addSql("INSERT INTO ticket_plan 
          (id, type_id, lessons_count, days_to_outdated, price, name)
            VALUES
          (5,2,8,31,0,'Подарочный абонемент на 8 занятий');
            ");

        $this->addSql("ALTER TABLE user_ticket ADD is_active bool DEFAULT 1 NOT NULL;");
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
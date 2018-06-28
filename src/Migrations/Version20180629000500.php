<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 29/06/2018
 * Time: 00:04
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180629000500 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql('
            INSERT INTO user (id, type_id, phone, name) VALUES (110, 3,  "+7(903)5527510", "Татьяна");
            INSERT INTO user_ticket (ticket_plan_id, user_id, lessons_expires) 
              VALUES (1, 110, 7);
        ');

        $this->addSql('
            INSERT INTO user (id, type_id, phone, name) VALUES (111, 3,  "+7(905)5968880", "Валерия");
            INSERT INTO user_ticket (ticket_plan_id, user_id, lessons_expires) 
              VALUES (1, 111, 7);
        ');

        $this->addSql('
            INSERT INTO user (id, type_id, phone, name) VALUES (112, 3,  "+7(919)1063619", "Мария");
            INSERT INTO user_ticket (ticket_plan_id, user_id, lessons_expires) 
              VALUES (1, 112, 8);
        ');

        $this->addSql('
            INSERT INTO user (id, type_id, phone, name) VALUES (113, 3,  "+7(925)3042486", "Донских Дарья");
            INSERT INTO user_ticket (ticket_plan_id, user_id, lessons_expires) 
              VALUES (1, 113, 8);
        ');

        $this->addSql('
            INSERT INTO user (id, type_id, phone, name) VALUES (114, 3,  "+7(960)6009998", "Анна");
            INSERT INTO user_ticket (ticket_plan_id, user_id, lessons_expires) 
              VALUES (1, 114, 8);
        ');

        $this->addSql('
            INSERT INTO user (id, type_id, phone, name) VALUES (115, 3,  "+7(916)0247264", "Полина");
            INSERT INTO user_ticket (ticket_plan_id, user_id, lessons_expires) 
              VALUES (1, 115, 0);
        ');
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
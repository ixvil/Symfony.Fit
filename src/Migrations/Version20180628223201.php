<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 28/06/2018
 * Time: 22:31
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180628223201 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql('UPDATE user SET name = "Кристина М." WHERE id = 27');
        $this->addSql('INSERT INTO user (id, type_id, phone, name) VALUES (100, 2, "-", "Кристина")');
        $this->addSql('UPDATE hall set name = "Зал #1" where id = 2');
        $this->addSql('UPDATE hall set name = "Зал #2" where id = 3');

        $this->addSql('
          INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (10 ,1, 100, null, 8);
                ');

        $this->addSql('
          INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (11 ,5, 100, null, 8);
                ');

        $this->addSql("
          INSERT INTO fit.lesson (hall_id, lesson_set_id, start_date_time) VALUES (2, 10, '2018-06-30 06:15:00');
          INSERT INTO fit.lesson (hall_id, lesson_set_id, start_date_time) VALUES (2, 10, '2018-07-07 06:15:00');
            ");

        $this->addSql("
          INSERT INTO fit.lesson (hall_id, lesson_set_id, start_date_time) VALUES (3, 2, '2018-06-30 06:15:00');
          INSERT INTO fit.lesson (hall_id, lesson_set_id, start_date_time) VALUES (3, 2, '2018-07-07 06:15:00');
            ");

        $this->addSql("
            UPDATE lesson set lesson_set_id = 11 WHERE id = 3489;        
        ");

    }

    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM user WHERE type_id = 2 and name = "Кристина"');
    }
}
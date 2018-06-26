<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 26/06/2018
 * Time: 09:39
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180626100000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql('delete from user_code');
        $this->addSql('delete from user_token');
        $this->addSql('DELETE from promo_code');
        $this->addSql('DELETE from user_ticket');


        $this->addSql('DELETE FROM lesson');
        $this->addSql('DELETE from lesson_set');
        $this->addSql('delete from user');


        $this->addSql('DELETE from lesson_type;');
        $this->addSql('
            INSERT INTO lesson_type (id, name, image) VALUES 
              (1, \'strong body + str\', \'https://ic.pics.livejournal.com/upryamka/7795106/299036/299036_original.png\');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (2, \'Aerostretching взрл\', \'http://potolki.elips.by/wp-content/uploads/potolki-color-215-200x150.jpg\');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (3, \'Stretching\', \'https://falcon-eyes.ru/upload/resize_cache/iblock/31c/255_258_1/31c8539def3f0c8fd1d25a983d2e90f2.jpg\');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (4, \'Воздушная акробатика детс\', \'https://womanadvice.ru/sites/default/files/ksenia_tr/nazvaniya_cvetov_i_ottenkov_12.png\');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (5, \'TRX\', \'http://www.stockvinil.ru/upload/shop_3/6/8/8/item_6883/shop_items_catalog_image6883.jpg\');
        ');
        $this->addSql('
            INSERT INTO fit.user (id, type_id, phone, name) VALUES (26, 2, \'-\', \'\');
        ');
        $this->addSql("
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (1, 1, 26, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (2, 2, 26, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (3, 3, 26, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (4, 4, 26, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (5, 5, 26, null, 8);
        ");

        $this->addSql("
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3360, 2, 1, '2018-06-25 05:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3361, 3, 2, '2018-06-26 05:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3362, 2, 1, '2018-06-27 08:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3363, 3, 2, '2018-06-28 05:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3364, 2, 3, '2018-06-29 05:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3365, 2, 1, '2018-06-30 05:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3366, 2, 1, '2018-07-01 05:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3367, 2, 3, '2018-06-25 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3368, 2, 5, '2018-06-25 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3369, 3, 3, '2018-06-25 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3370, 2, 3, '2018-06-26 06:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3371, 3, 4, '2018-06-26 06:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3372, 2, 1, '2018-06-26 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3373, 3, 2, '2018-06-26 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3374, 2, 3, '2018-06-27 13:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3375, 2, 1, '2018-06-27 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3376, 3, 3, '2018-06-27 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3377, 2, 5, '2018-06-27 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3378, 3, 1, '2018-06-27 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3379, 3, 4, '2018-06-28 06:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3380, 2, 1, '2018-06-28 13:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3381, 2, 3, '2018-06-28 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3382, 3, 2, '2018-06-28 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3383, 2, 5, '2018-06-29 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3384, 3, 3, '2018-06-29 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3385, 2, 3, '2018-06-30 06:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3386, 2, 1, '2018-06-30 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3387, 2, 3, '2018-06-30 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3388, 2, 1, '2018-07-01 06:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3389, 2, 1, '2018-07-01 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3390, 2, 3, '2018-07-01 15:45:00');
        ");

        $this->addSql("
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time)
              (select null, hall_id, lesson_set_id, from_unixtime(unix_timestamp(start_date_time)+604800) from lesson ); 
        ");

        $this->addSql("
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time)
              (select null, hall_id, lesson_set_id, from_unixtime(unix_timestamp(start_date_time)+1209600) from lesson );
        ");

    }


    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
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
        $this->addSql("
            INSERT INTO lesson_type (id, name, image) VALUES 
              (1, 'strong body + str', 'https://ic.pics.livejournal.com/upryamka/7795106/299036/299036_original.png');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (2, 'Aerostretching взрл', 'http://potolki.elips.by/wp-content/uploads/potolki-color-215-200x150.jpg');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (3, 'Stretching', 'https://falcon-eyes.ru/upload/resize_cache/iblock/31c/255_258_1/31c8539def3f0c8fd1d25a983d2e90f2.jpg');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (4, 'Воздушная акробатика детс', 'http://vitrage.su/wa-data/public/shop/products/41/01/141/images/188/188.750x0.jpg');
            INSERT INTO lesson_type (id, name, image) VALUES 
              (5, 'TRX', 'http://www.stockvinil.ru/upload/shop_3/6/8/8/item_6883/shop_items_catalog_image6883.jpg');
        ");
        $this->addSql("
            INSERT INTO fit.user (id, type_id, phone, name) VALUES (26, 2, '-', 'Яна');
            INSERT INTO fit.user (id, type_id, phone, name) VALUES (27, 2, '-', 'Кристина');
            INSERT INTO fit.user (id, type_id, phone, name) VALUES (28, 2, '-', 'Юля');
            INSERT INTO fit.user (id, type_id, phone, name) VALUES (29, 2, '-', 'Артем');
        ");
        $this->addSql("
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (1, 1, 26, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (2, 2, 27, null, 5);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (3, 3, 26, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (4, 4, 27, null, 5);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (5, 5, 29, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (6, 1, 27, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (7, 3, 27, null, 8);
            INSERT INTO fit.lesson_set (id, lesson_type_id, trainer_user_id, name, users_limit) 
                VALUES (8, 3, 28, null, 8);
            
        ");

        $this->addSql("
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3360, 2, 1, '2018-06-25 06:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3361, 3, 2, '2018-06-26 06:15:00');
            
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3363, 3, 2, '2018-06-28 06:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3364, 2, 3, '2018-06-29 06:15:00');
            
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3367, 2, 8, '2018-06-25 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3368, 2, 5, '2018-06-25 16:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3369, 3, 8, '2018-06-25 16:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3370, 2, 3, '2018-06-26 07:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3371, 3, 4, '2018-06-26 07:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3372, 2, 6, '2018-06-26 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3373, 3, 2, '2018-06-26 16:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3374, 2, 8, '2018-06-27 14:45:00');
            
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3376, 3, 8, '2018-06-27 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3377, 2, 5, '2018-06-27 16:45:00');
            
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3379, 3, 4, '2018-06-28 07:15:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3380, 2, 6, '2018-06-28 14:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3381, 2, 7, '2018-06-28 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3382, 3, 2, '2018-06-28 16:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3383, 2, 5, '2018-06-29 16:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3384, 3, 8, '2018-06-29 16:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3386, 2, 1, '2018-06-30 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3387, 2, 3, '2018-06-30 16:45:00');
            
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3389, 2, 1, '2018-07-01 15:45:00');
            INSERT INTO fit.lesson (id, hall_id, lesson_set_id, start_date_time) VALUES (3390, 2, 3, '2018-07-01 16:45:00');
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
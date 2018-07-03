<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 02/07/2018
 * Time: 00:13
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180702001300 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE payment_order_status
            (
                id int PRIMARY KEY AUTO_INCREMENT,
                name varchar(255) NOT NULL
            );
        ');

        $this->addSql('
            INSERT INTO fit.payment_order_status (id, name) VALUES (1, \'new\');
            INSERT INTO fit.payment_order_status (id, name) VALUES (2, \'paid\');
            INSERT INTO fit.payment_order_status (id, name) VALUES (3, \'canceled\');
        ');

        $this->addSql('
            CREATE TABLE payment_order
            (
                id int PRIMARY KEY AUTO_INCREMENT,
                created_at datetime,
                updated_at datetime,
                status_id int,
                ticket_plan_id int,
                user_id int,
                amount int,
                user_ticket_id int DEFAULT null ,
                CONSTRAINT payment_order_payment_order_status_id_fk FOREIGN KEY (status_id) REFERENCES payment_order_status (id),
                CONSTRAINT payment_order_ticket_plan_id_fk FOREIGN KEY (ticket_plan_id) REFERENCES ticket_plan (id),
                CONSTRAINT payment_order_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id),
                CONSTRAINT payment_order_user_ticket_id_fk FOREIGN KEY (user_ticket_id) REFERENCES user_ticket (id)
            );
        ');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP table payment_order');
        $this->addSql('DROP table payment_order_status');
    }
}
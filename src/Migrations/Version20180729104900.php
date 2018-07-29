<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 29/07/2018
 * Time: 10:49
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180729104900 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE discount
            (
                id int PRIMARY KEY AUTO_INCREMENT,
                user_id int DEFAULT NULL ,
                ticket_plan_id int DEFAULT NULL ,
                active_from datetime NOT NULL,
                active_to datetime NOT NULL,
                value int DEFAULT 0,
                CONSTRAINT discount_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id),
                CONSTRAINT discount_ticket_plan_id_fk FOREIGN KEY (ticket_plan_id) REFERENCES ticket_plan (id)
            );
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP TABLE discount;
        ");
    }
}
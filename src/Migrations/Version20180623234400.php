<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 23/06/2018
 * Time: 23:44
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


class Version20180623234400 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql('
          CREATE TABLE promo_code
          (
            id int PRIMARY KEY AUTO_INCREMENT,
            code varchar(255) NOT NULL,
            ticket_plan_id int NOT NULL,
            CONSTRAINT promo_code_ticket_plan_id_fk FOREIGN KEY (ticket_plan_id) REFERENCES ticket_plan (id)
          );
        ');

        $this->addSql('
          CREATE UNIQUE INDEX promo_code_code_uindex ON promo_code (code);
        ');

        $this->addSql('
            ALTER TABLE promo_code ADD is_activated bool DEFAULT false  NULL;
            ALTER TABLE promo_code ADD activated_by_id int NULL;
            ALTER TABLE promo_code
            ADD CONSTRAINT promo_code_user_id_fk
            FOREIGN KEY (activated_by_id) REFERENCES user (id);
        ');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE promo_code');
    }
}
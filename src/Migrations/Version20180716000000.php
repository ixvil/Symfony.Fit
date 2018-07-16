<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 16/07/2018
 * Time: 00:01
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180716000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE lesson_user_status
            (
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL
            );
        ");

        $this->addSql("
            CREATE UNIQUE INDEX lesson_user_status_name_uindex ON lesson_user_status (name);
        ");

        $this->addSql("INSERT INTO lesson_user_status (id, name) VALUES (2, 'approved');");
        $this->addSql("INSERT INTO lesson_user_status (id, name) VALUES (3, 'canceled');");
        $this->addSql("INSERT INTO lesson_user_status (id, name) VALUES (1, 'new');");

        $this->addSql("
            ALTER TABLE lesson_user ADD status_id int NOT NULL DEFAULT 1;
        ");
        $this->addSql("
            ALTER TABLE lesson_user
            ADD CONSTRAINT lesson_user_lesson_user_status_id_fk
            FOREIGN KEY (status_id) REFERENCES lesson_user_status (id);
        ");

    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE lesson_user DROP FOREIGN KEY lesson_user_lesson_user_status_id_fk;
        ");
        $this->addSql("
            DROP INDEX lesson_user_lesson_user_status_id_fk ON lesson_user;
        ");
        $this->addSql("
            ALTER TABLE lesson_user DROP status_id;
        ");
        $this->addSql("DROP TABLE lesson_user_status");
    }
}
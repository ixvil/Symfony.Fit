<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 10/06/2018
 * Time: 22:55
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180610225500 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE user_code
            (
                id int AUTO_INCREMENT PRIMARY KEY,
                user_id int,
                code int NOT NULL,
                timestamp timestamp DEFAULT current_timestamp,
                is_used tinyint DEFAULT 0,
                CONSTRAINT user_codes_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id)
            );
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE user_code;");
    }
}
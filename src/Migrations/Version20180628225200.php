<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 28/06/2018
 * Time: 22:51
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180628225200 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("
            UPDATE lesson set lesson_set_id = 11 WHERE id = 3865;        
        ");

        $this->addSql("
            UPDATE lesson_type set image = 'http://krasivayaideya.ru/pictures/product/big/6223_big.jpg' where id = 1;    
        ");
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
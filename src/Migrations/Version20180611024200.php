<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/06/2018
 * Time: 02:41
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180611024200 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql('
          CREATE TABLE `user_token` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `token` varchar(255) NOT NULL,
              `is_active` tinyint(4) NOT NULL DEFAULT \'1\',
              PRIMARY KEY (`id`),
              KEY `user_tokens_user_id_fk` (`user_id`),
              CONSTRAINT `user_tokens_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE user_token');
        // TODO: Implement down() method.
    }
}
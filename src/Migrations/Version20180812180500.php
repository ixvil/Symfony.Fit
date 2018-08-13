<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 12/08/2018
 * Time: 18:05
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180812180500 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql(
            [
                'ALTER TABLE user ADD bonus_balance int DEFAULT 0 NULL;',
                'ALTER TABLE payment_order ADD bonus_amount int DEFAULT 0 NULL;'
            ]
        );
    }

    public function down(Schema $schema)
    {
        $this->addSql(
            [
                'ALTER TABLE user DROP bonus_balance;',
                'ALTER TABLE payment_order DROP bonus_amount;'
            ]
        );
    }
}
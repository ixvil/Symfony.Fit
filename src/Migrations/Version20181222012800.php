<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 22/12/2018
 * Time: 01:28
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181222012800 extends AbstractMigration
{

	public function up(Schema $schema)
	{
		$this->addSql("ALTER TABLE promo_code ADD bonus_amount int DEFAULT 0 NOT NULL;");
		$this->addSql("ALTER TABLE promo_code MODIFY ticket_plan_id int(11) DEFAULT null;");
	}

	public function down(Schema $schema)
	{
		$this->addSql("ALTER TABLE promo_code MODIFY ticket_plan_id int(11) NOT NULL");
		$this->addSql("ALTER TABLE promo_code DROP bonus_amount;");
	}
}
<?php


namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20191101194900 extends AbstractMigration
{

	public function up(Schema $schema)
	{
		$this->addSql("ALTER TABLE payment_order ADD bank_payment_id int DEFAULT NULL;");

	}

	public function down(Schema $schema)
	{
		$this->addSql("ALTER TABLE payment_order DROP bank_payment_id;");
	}
}
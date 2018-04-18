<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180418234055 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_ticket (id INT AUTO_INCREMENT NOT NULL, ticket_plan_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date_created_at DATETIME DEFAULT NULL, lessons_expires INT DEFAULT NULL, INDEX user_tickets_ticket_plans_id_fk (ticket_plan_id), INDEX user_tickets_users_id_fk (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, phone VARCHAR(64) DEFAULT NULL, name VARCHAR(256) DEFAULT NULL, INDEX users_user_types_id_fk (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, hall_id INT DEFAULT NULL, lesson_set_id INT DEFAULT NULL, INDEX lessons_halls_id_fk (hall_id), INDEX lessons_lesson_sets_id_fk (lesson_set_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_user (id INT AUTO_INCREMENT NOT NULL, lesson_id INT DEFAULT NULL, user_ticket_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX lesson_users_lessons_id_fk (lesson_id), INDEX lesson_users_users_id_fk (user_id), INDEX lesson_users_user_tickets_id_fk (user_ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hall (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_plan (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, lessons_count INT DEFAULT NULL, days_to_outdated INT DEFAULT NULL, price INT DEFAULT NULL, name VARCHAR(256) DEFAULT NULL, INDEX ticket_plans_ticket_plan_types_id_fk (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_set (id INT AUTO_INCREMENT NOT NULL, lesson_type_id INT DEFAULT NULL, trainer_user_id INT DEFAULT NULL, name VARCHAR(256) DEFAULT NULL, INDEX lesson_sets_users_id_fk (trainer_user_id), INDEX lesson_set_lesson_type_id_fk (lesson_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_plan_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_ticket ADD CONSTRAINT FK_F2F2B69EE4555BC9 FOREIGN KEY (ticket_plan_id) REFERENCES ticket_plan (id)');
        $this->addSql('ALTER TABLE user_ticket ADD CONSTRAINT FK_F2F2B69EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C54C8C93 FOREIGN KEY (type_id) REFERENCES user_type (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F352AFCFD6 FOREIGN KEY (hall_id) REFERENCES hall (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3636DFD4F FOREIGN KEY (lesson_set_id) REFERENCES lesson_set (id)');
        $this->addSql('ALTER TABLE lesson_user ADD CONSTRAINT FK_B4E2102DCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE lesson_user ADD CONSTRAINT FK_B4E2102D70E0CA36 FOREIGN KEY (user_ticket_id) REFERENCES user_ticket (id)');
        $this->addSql('ALTER TABLE lesson_user ADD CONSTRAINT FK_B4E2102DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_plan ADD CONSTRAINT FK_EF814E45C54C8C93 FOREIGN KEY (type_id) REFERENCES ticket_plan_type (id)');
        $this->addSql('ALTER TABLE lesson_set ADD CONSTRAINT FK_9461833F3030DE34 FOREIGN KEY (lesson_type_id) REFERENCES lesson_type (id)');
        $this->addSql('ALTER TABLE lesson_set ADD CONSTRAINT FK_9461833F6B51EA5A FOREIGN KEY (trainer_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lesson_user DROP FOREIGN KEY FK_B4E2102D70E0CA36');
        $this->addSql('ALTER TABLE lesson_set DROP FOREIGN KEY FK_9461833F3030DE34');
        $this->addSql('ALTER TABLE user_ticket DROP FOREIGN KEY FK_F2F2B69EA76ED395');
        $this->addSql('ALTER TABLE lesson_user DROP FOREIGN KEY FK_B4E2102DA76ED395');
        $this->addSql('ALTER TABLE lesson_set DROP FOREIGN KEY FK_9461833F6B51EA5A');
        $this->addSql('ALTER TABLE lesson_user DROP FOREIGN KEY FK_B4E2102DCDF80196');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F352AFCFD6');
        $this->addSql('ALTER TABLE user_ticket DROP FOREIGN KEY FK_F2F2B69EE4555BC9');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C54C8C93');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3636DFD4F');
        $this->addSql('ALTER TABLE ticket_plan DROP FOREIGN KEY FK_EF814E45C54C8C93');
        $this->addSql('DROP TABLE user_ticket');
        $this->addSql('DROP TABLE lesson_type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_user');
        $this->addSql('DROP TABLE hall');
        $this->addSql('DROP TABLE ticket_plan');
        $this->addSql('DROP TABLE user_type');
        $this->addSql('DROP TABLE lesson_set');
        $this->addSql('DROP TABLE ticket_plan_type');
    }
}

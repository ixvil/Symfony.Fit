<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 09/08/2018
 * Time: 03:28
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180809032800 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $basic = [38087, 23448, 85426, 78901, 45029, 18251, 79017, 72239, 36887, 96106, 88985, 81319, 53224, 78890,
                  29621, 12532, 38320, 84683, 88297, 88130, 78502, 14744, 15400, 77467, 21462, 72804, 35405, 25285,
                  34221, 99917, 77728, 71834, 85374, 18583, 22888, 45818, 80026, 98183, 41421, 90025, 39071, 20645,
                  35184, 34912, 83622, 95125, 97194, 28790, 66869, 66252,];

        $wax = [21876, 29218, 84946, 16770, 32763, 93474, 59313, 93424, 52384, 29794, 14879, 33079, 34266, 87577, 60132,
                43455, 74229, 51937, 40297, 64610, 94811, 73972, 78895, 57918, 18622, 67677, 28933, 24572, 26870, 74059,
                20017, 42170, 19570, 82370, 46294, 80365, 48576, 91024, 33814, 67863, 39224, 46869, 51213, 59913, 66728,
                30485, 40541, 86575, 54507, 59055];

        $goodHair = [52233, 40176, 68456, 49975, 91489, 80839, 41071, 79732, 20917, 94285, 46216, 15423, 76912, 64112,
                     60200, 23350, 94252, 32145, 48721, 20650, 70805, 64185, 62862, 80438, 97632, 92990, 85554, 74402,
                     44614, 50935, 51850, 66671, 89105, 15402, 16035, 33607, 47521, 15147, 80914, 35422, 85283, 59376,
                     60424, 58432, 68598, 52430, 67287, 73190, 52159, 28987];

        $this->addSql(
            [
                " INSERT INTO `ticket_plan` (id, `type_id`, `lessons_count`, `days_to_outdated`, `price`, `name`) 
                    VALUES (9, 2, 1, 31, 0, 'Подарочное занятие Wax&Nails Basic') ",
                " INSERT INTO `ticket_plan` (id, `type_id`, `lessons_count`, `days_to_outdated`, `price`, `name`) 
                    VALUES (10, 2, 1, 31, 0, 'Подарочное занятие Wax&Nails ') ",
                " INSERT INTO `ticket_plan` (id, `type_id`, `lessons_count`, `days_to_outdated`, `price`, `name`) 
                    VALUES (11, 2, 1, 31, 0, 'Подарочное занятие Good Hair Day') ",
            ]
        );

        foreach ($basic as $code) {
            $this->addSql(
                "INSERT INTO `promo_code` (`code`, `ticket_plan_id`, `is_activated`, `activated_by_id`) 
                  VALUES ('{$code}', 9, 0, NULL)"
            );
        }

        foreach ($wax as $code) {
            $this->addSql(
                "INSERT INTO `promo_code` (`code`, `ticket_plan_id`, `is_activated`, `activated_by_id`) 
                  VALUES ('{$code}', 10, 0, NULL)"
            );
        }

        foreach ($goodHair as $code) {
            $this->addSql(
                "INSERT INTO `promo_code` (`code`, `ticket_plan_id`, `is_activated`, `activated_by_id`) 
                  VALUES ('{$code}', 11, 0, NULL)"
            );
        }
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
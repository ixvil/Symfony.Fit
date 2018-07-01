<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 01/07/2018
 * Time: 11:27
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180701112600 extends AbstractMigration
{

    public function up(Schema $schema)
    {
       $this->addSql('UPDATE lesson_type set image = "//stretchandgo.ru/img/5.jpg" where id = 1;');
       $this->addSql('UPDATE lesson_type set image = "//stretchandgo.ru/img/4.jpg" where id = 2;');
       $this->addSql('UPDATE lesson_type set image = "//stretchandgo.ru/img/1.jpg" where id = 3;');
       $this->addSql('UPDATE lesson_type set image = "//stretchandgo.ru/img/3.jpg" where id = 4;');
       $this->addSql('UPDATE lesson_type set image = "//stretchandgo.ru/img/2.jpg" where id = 5;');
    }

    public function down(Schema $schema)
    {
        $this->addSql('UPDATE lesson_type set image = "http://krasivayaideya.ru/pictures/product/big/6223_big.jpg" where id = 1;');
        $this->addSql('UPDATE lesson_type set image = "http://potolki.elips.by/wp-content/uploads/potolki-color-215-200x150.jpg" where id = 2;');
        $this->addSql('UPDATE lesson_type set image = "https://falcon-eyes.ru/upload/resize_cache/iblock/31c/255_258_1/31c8539def3f0c8fd1d25a983d2e90f2.jpg" where id = 3;');
        $this->addSql('UPDATE lesson_type set image = "http://vitrage.su/wa-data/public/shop/products/41/01/141/images/188/188.750x0.jpg" where id = 4;');
        $this->addSql('UPDATE lesson_type set image = "http://www.stockvinil.ru/upload/shop_3/6/8/8/item_6883/shop_items_catalog_image6883.jpg" where id = 5;');
    }
}
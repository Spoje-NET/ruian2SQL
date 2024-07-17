ruian2SQL
=========

Nástroj pro import **adresních míst Státní správy zeměměřictví a katastru**  do SQL databáze

[Formát vstupních dat](Adresni-mista-CSV_atributy.pdf?raw=true)

(https://www.cuzk.cz/Uvod/Produkty-a-sluzby/RUIAN/2-Poskytovani-udaju-RUIAN-ISUI-VDP/Dopady-zmeny-zakona-c-51-2020-Sb/Adresni-mista-CSV_atributy.aspx)

[![time tracker](https://wakatime.com/badge/github/Spoje-NET/ruian2SQL.svg)](https://wakatime.com/badge/github/Spoje-NET/ruian2SQL)

Požadavky
---------

* **[PHP](http://php.net/)7+** (testováno na 7.2) - popular general-purpose scripting language
* **SQL databáze** - podporovaná nástrojem [phinx](http://docs.phinx.org/en/latest/configuration.html#supported-adapters): MySQL,PostgreSQL,SQLite,MsSQL
* [composer](https://getcomposer.org/) - Dependency Manager for PHP 
* [phing](https://www.phing.info/) - a PHP project build system or build tool based on Apache Ant.
* [phinx](https://phinx.org/) - PHP Database Migrations For Everyone


Postup 
======

Nejprve je třeba si připravit databázi: 

MySQL
-----

Aby se data naimportovala je třeba nastavit hodnotu **max_allowed_packet** na 100M 

```
mysqladmin -u root -p create ruian_devel
mysql -u root -p -e "GRANT ALL PRIVILEGES ON ruian_devel.* TO 'ruian'@'localhost' IDENTIFIED BY 'ruian'"
```

PostgreSQL
----------

```
sudo -u postgres bash -c "psql -c \"CREATE USER ruian WITH PASSWORD 'ruian';\""
sudo -u postgres bash -c "psql -c \"create database ruian with owner ruian encoding='utf8' template template0;\""
```

Přihlašovací údaje do databáze je třeba zadat do souboru [phinx.yml]


Spuštění
--------

Pokud nepoužíváme globálně dostupný příkaz **phinx** je třeba jej doinstalovat. 

composer install

Spolu s ním se doinstaluje i lokální verze příkazu **phing**

Import dat do databáze zahájme poté příkazem **phing** nebop **./vendor/bin/phing**

1. Tento stahne z internetu archiv [adresních míst](https://nahlizenidokn.cuzk.cz/StahniAdresniMistaRUIAN.aspx).
1. Rozbalí jej
1. [Vytvoří v databázi prázdnou tabulku **places**](db/migrations/20190316175201_places.php) (příkaz **phinx migrate**)
1. [Naimportuje data z rozbalených  CSV souborů](db/seeds/Places.php) (příkaz **phinx seed:run -s Places**)

Pokud vše dopadne dobře, najdete v databázi tabuku **ruian_devel** obsahující **2.921.779** záznamů:

Struktura Databáze
------------------

```sql
CREATE TABLE `places` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `adm` int(11) unsigned DEFAULT NULL COMMENT 'Kód ADM',
  `code` int(11) unsigned DEFAULT NULL COMMENT 'Kód obce',
  `name` varchar(255) DEFAULT NULL COMMENT 'Název obce',
  `momc_code` int(11) unsigned DEFAULT NULL COMMENT 'Kód MOMC',
  `momc_name` varchar(255) DEFAULT NULL COMMENT 'Název MOMC',
  `mop_code` int(11) unsigned DEFAULT NULL COMMENT 'Kód MOP',
  `mop_name` varchar(255) DEFAULT NULL COMMENT 'Název MOP',
  `district_code` int(11) unsigned DEFAULT NULL COMMENT 'Kód části obce',
  `district_name` varchar(255) DEFAULT NULL COMMENT 'Název části obce',
  `street_code` int(11) unsigned DEFAULT NULL COMMENT 'Kód ulice',
  `street_name` varchar(255) DEFAULT NULL COMMENT 'Název ulice',
  `so_type` varchar(255) DEFAULT NULL COMMENT 'Typ SO',
  `house_number` int(11) unsigned DEFAULT NULL COMMENT 'Číslo domovní',
  `orientation_number` int(11) unsigned DEFAULT NULL COMMENT 'Číslo orientační',
  `orientation_number_mark` varchar(255) DEFAULT NULL COMMENT 'Znak čísla orientačního',
  `zip` varchar(5) DEFAULT NULL COMMENT 'PSČ',
  `y` float unsigned DEFAULT NULL COMMENT 'S-JTSK Souřadnice Y',
  `x` float unsigned DEFAULT NULL COMMENT 'S-JTSK Souřadnice X',
  `lat` decimal(30,10) unsigned DEFAULT NULL COMMENT 'WGS 84 Y',
  `long` decimal(30,10) unsigned DEFAULT NULL COMMENT 'WGS 84 X',
  `valid_from` datetime DEFAULT NULL COMMENT 'Platí Od',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_adm` (`adm`),
  KEY `idx_code` (`code`),
  KEY `idx_name` (`name`),
  KEY `idx_stret_name` (`street_name`),
  KEY `idx_house_number` (`house_number`),
  KEY `idx_orientation_number` (`orientation_number`),
  KEY `idx_zip` (`zip`),
  KEY `gps` (`x`,`y`),
  KEY `gps_reverse` (`y`,`x`)
) ENGINE=InnoDB AUTO_INCREMENT=2921780 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Adresní místa z celé ČR';
```

![PhpMyAdmin](phpmya.png?raw=true)

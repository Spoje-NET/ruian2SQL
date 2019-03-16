ruian2SQL
=========

Nástroj pro import **adresních míst Státní správy zeměměřictví a katastru**  do SQL databáze


Požadavky
---------

* **[PHP](http://php.net/)7+** (testováno na 7.2) - popular general-purpose scripting language
* **SQL databáze** - podporovaná nástrojem [phinx](http://docs.phinx.org/en/latest/configuration.html#supported-adapters): MySQL,PostgreSQL,SQLite,MsSQL
* [composer](https://getcomposer.org/) - Dependency Manager for PHP 
* [phing](https://www.phing.info/) - a PHP project build system or build tool based on Apache Ant.
* [phinx](https://phinx.org/) - PHP Database Migrations For Everyone


##Postup 

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
  `id` int(11) NOT NULL,
  `adm` int(11) UNSIGNED NOT NULL COMMENT 'Kód ADM',
  `code` int(11) UNSIGNED NOT NULL COMMENT 'Kód obce',
  `name` varchar(255) NOT NULL COMMENT 'Název obce',
  `momc_code` int(11) UNSIGNED NOT NULL COMMENT 'Kód MOMC',
  `momc_name` varchar(255) NOT NULL COMMENT 'Název MOMC',
  `mop_code` int(11) UNSIGNED NOT NULL COMMENT 'Kód MOP',
  `mop_name` varchar(255) NOT NULL COMMENT 'Název MOP',
  `district_code` int(11) UNSIGNED NOT NULL COMMENT 'Kód části obce',
  `district_name` varchar(255) NOT NULL COMMENT 'Název části obce',
  `street_code` int(11) UNSIGNED NOT NULL COMMENT 'Kód ulice',
  `street_name` varchar(255) NOT NULL COMMENT 'Název ulice',
  `so_type` varchar(255) NOT NULL COMMENT 'Typ SO',
  `house_number` int(11) UNSIGNED NOT NULL COMMENT 'Číslo domovní',
  `orientation_number` int(11) UNSIGNED NOT NULL COMMENT 'Číslo orientační',
  `orientation_number_mark` varchar(255) NOT NULL COMMENT 'Znak čísla orientačního',
  `zip` varchar(5) NOT NULL COMMENT 'PSČ',
  `y` decimal(10,0) UNSIGNED NOT NULL COMMENT 'Souřadnice Y',
  `x` decimal(10,0) UNSIGNED NOT NULL COMMENT 'Souřadnice X',
  `valid_from` datetime NOT NULL COMMENT 'Platí Od'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Adresní místa z celé ČR';

ALTER TABLE `places`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_adm` (`adm`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_stret_name` (`street_name`),
  ADD KEY `idx_house_number` (`house_number`),
  ADD KEY `idx_orientation_number` (`orientation_number`),
  ADD KEY `idx_zip` (`zip`);

```


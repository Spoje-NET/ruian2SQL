<?php

use Phinx\Migration\AbstractMigration;

class Places extends AbstractMigration {

    /**
     */
    public function change() {
        $table = $this->table('places', ['comment' => 'Adresní místa z celé ČR']);
        $table
                ->addColumn('adm', 'integer',
                        ['comment' => 'Kód ADM', 'signed' => false])
                ->addColumn('code', 'integer',
                        ['comment' => 'Kód obce', 'signed' => false])
                ->addColumn('name', 'string', ['comment' => 'Název obce'])
                ->addColumn('momc_code', 'integer',
                        ['comment' => 'Kód MOMC', 'signed' => false, 'null' => true])
                ->addColumn('momc_name', 'string', ['comment' => 'Název MOMC'])
                ->addColumn('mop_code', 'integer',
                        ['comment' => 'Kód MOP', 'signed' => false])
                ->addColumn('mop_name', 'string', ['comment' => 'Název MOP'])
                ->addColumn('district_code', 'integer',
                        ['comment' => 'Kód části obce', 'signed' => false])
                ->addColumn('district_name', 'string',
                        ['comment' => 'Název části obce'])
                ->addColumn('street_code', 'integer',
                        ['comment' => 'Kód ulice', 'signed' => false])
                ->addColumn('street_name', 'string', ['comment' => 'Název ulice'])
                ->addColumn('so_type', 'string', ['comment' => 'Typ SO'])
                ->addColumn('house_number', 'integer',
                        ['comment' => 'Číslo domovní', 'signed' => false])
                ->addColumn('orientation_number', 'integer',
                        ['comment' => 'Číslo orientační', 'signed' => false, 'null' => true])
                ->addColumn('orientation_number_mark', 'string',
                        ['comment' => 'Znak čísla orientačního'])
                ->addColumn('zip', 'string',
                        ['comment' => 'PSČ', 'signed' => false, 'limit' => 5])
                ->addColumn('y', 'float', ['comment' => 'S-JTSK Souřadnice Y', 'signed' => false])
                ->addColumn('x', 'float', ['comment' => 'S-JTSK Souřadnice X', 'signed' => false])
                ->addColumn('lat', 'decimal', ['comment' => 'WGS 84 Y', 'signed' => false, 'scale'=>10, 'precision' => 30])
                ->addColumn('long', 'decimal', ['comment' => 'WGS 84 X', 'signed' => false, 'scale'=>10, 'precision' => 30])
                ->addColumn('valid_from', 'datetime', ['comment' => 'Platí Od'])
                ->addIndex(['adm'], ['unique' => true, 'name' => 'idx_adm'])
                ->addIndex(['code'], ['name' => 'idx_code'])
                ->addIndex(['name'], ['name' => 'idx_name'])
                ->addIndex(['street_name'], ['name' => 'idx_stret_name'])
                ->addIndex(['house_number'], ['name' => 'idx_house_number'])
                ->addIndex(['orientation_number'], ['name' => 'idx_orientation_number'])
                ->addIndex(['zip'], ['name' => 'idx_zip'])
                ->addIndex(['x', 'y'], ['name' => 'gps'])
                ->addIndex(['y', 'x'], ['name' => 'gps_reverse'])
                ->create();
    }
}

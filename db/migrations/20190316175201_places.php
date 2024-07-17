<?php

use Phinx\Migration\AbstractMigration;

class Places extends AbstractMigration {

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
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
                ->addColumn('y', 'decimal',
                        ['comment' => 'Souřadnice Y', 'signed' => false, 'precision' => 2])
                ->addColumn('x', 'decimal',
                        ['comment' => 'Souřadnice X', 'signed' => false, 'precision' => 2])
                ->addColumn('valid_from', 'datetime', ['comment' => 'Platí Od'])
                ->addIndex(['adm'], ['unique' => true, 'name' => 'idx_adm'])
                ->addIndex(['code'], ['name' => 'idx_code'])
                ->addIndex(['name'], ['name' => 'idx_name'])
                ->addIndex(['street_name'], ['name' => 'idx_stret_name'])
                ->addIndex(['house_number'], ['name' => 'idx_house_number'])
                ->addIndex(['orientation_number'],
                        ['name' => 'idx_orientation_number'])
                ->addIndex(['zip'], ['name' => 'idx_zip'])
                ->create();
    }
}

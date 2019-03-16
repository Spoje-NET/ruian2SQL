<?php

/**
 * Import Places
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
use Phinx\Seed\AbstractSeed;

class Places extends AbstractSeed
{

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $allFiles = scandir('build/CSV/');
        $posts    = $this->table('places');
        $data     = [];
        foreach ($allFiles as $filePos => $dirInfo) {
            if (is_file('build/CSV/'.$dirInfo)) {
                echo " $filePos / ".count($allFiles)." ".$dirInfo;
                foreach (file('build/CSV/'.$dirInfo) as $pos => $inputRowText) {
                    if ($pos != 0) { //Skip Header Row
                        $inputRow = str_getcsv(iconv('windows-1250', 'UTF-8',
                                $inputRowText), ';');

                        $offset = 0;

                        $data[] = [
                            'adm' => $inputRow[$offset++],
                            'code' => $inputRow[$offset++],
                            'name' => $inputRow[$offset++],
                            'momc_code' => $inputRow[$offset++],
                            'momc_name' => $inputRow[$offset++],
                            'mop_code' => $inputRow[$offset++],
                            'mop_name' => $inputRow[$offset++],
                            'district_code' => $inputRow[$offset++],
                            'district_name' => $inputRow[$offset++],
                            'street_code' => $inputRow[$offset++],
                            'street_name' => $inputRow[$offset++],
                            'so_type' => $inputRow[$offset++],
                            'house_number' => $inputRow[$offset++],
                            'orientation_number' => $inputRow[$offset++],
                            'orientation_number_mark' => $inputRow[$offset++],
                            'zip' => $inputRow[$offset++],
                            'y' => $inputRow[$offset++],
                            'x' => $inputRow[$offset++],
                        ];

                        echo '.';
                    }
                }
                echo "\n";
                $posts->insert($data)->save();
                unset($data);
            }
        }

    }
}

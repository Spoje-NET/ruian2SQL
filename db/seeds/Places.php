<?php

/**
 * Import Places
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
use Phinx\Seed\AbstractSeed;

class Places extends AbstractSeed {

    private function tsktowgs84($X, $Y, $H) {
        if ($X < 0 && $Y < 0) {
            $X = -$X;
            $Y = -$Y;
        }

        if ($Y > $X) {
            $temp = $X;
            $X = $Y;
            $Y = $temp;
        }

        $a = 6377397.15508;
        $e = 0.081696831215303;
        $n = 0.97992470462083;
        $konstURo = 12310230.12797036;
        $sinUQ = 0.863499969506341;
        $cosUQ = 0.504348889819882;
        $sinVQ = 0.420215144586493;
        $cosVQ = 0.907424504992097;
        $alfa = 1.000597498371542;
        $k = 1.003419163966575;

        $ro = sqrt($X * $X + $Y * $Y);
        $epsilon = 2 * atan($Y / ($ro + $X));

        $D = $epsilon / $n;

        $S = 2 * atan(exp(1 / $n * log($konstURo / $ro))) - M_PI / 2;

        $sinS = sin($S);
        $cosS = cos($S);
        $sinU = $sinUQ * $sinS - $cosUQ * $cosS * cos($D);
        $cosU = sqrt(1 - $sinU * $sinU);
        $sinDV = sin($D) * $cosS / $cosU;
        $cosDV = sqrt(1 - $sinDV * $sinDV);
        $sinV = $sinVQ * $cosDV - $cosVQ * $sinDV;
        $cosV = $cosVQ * $cosDV + $sinVQ * $sinDV;
        $Ljtsk = 2 * atan($sinV / (1 + $cosV)) / $alfa;
        $t = exp(2 / $alfa * log((1 + $sinU) / $cosU / $k));
        $pom = ($t - 1) / ($t + 1);

        do {
            $sinB = $pom;
            $pom = $t * exp($e * log((1 + $e * $sinB) / (1 - $e * $sinB)));
            $pom = ($pom - 1) / ($pom + 1);
        } while (abs($pom - $sinB) > 1e-15);

        $Bjtsk = atan($pom / sqrt(1 - $pom * $pom));

        $a = 6377397.15508;
        $f1 = 299.152812853;
        $e2 = 1 - (1 - 1 / $f1) * (1 - 1 / $f1);
        $ro = $a / sqrt(1 - $e2 * sin($Bjtsk) * sin($Bjtsk));
        $x = ($ro + $H) * cos($Bjtsk) * cos($Ljtsk);
        $y = ($ro + $H) * cos($Bjtsk) * sin($Ljtsk);
        $z = ((1 - $e2) * $ro + $H) * sin($Bjtsk);

        $dx = 570.69;
        $dy = 85.69;
        $dz = 462.84;
        $wz = -5.2611 / 3600 * M_PI / 180;
        $wy = -1.58676 / 3600 * M_PI / 180;
        $wx = -4.99821 / 3600 * M_PI / 180;
        $m = 3.543e-6;
        $xn = $dx + (1 + $m) * ($x + $wz * $y - $wy * $z);
        $yn = $dy + (1 + $m) * (-$wz * $x + $y + $wx * $z);
        $zn = $dz + (1 + $m) * ($wy * $x - $wx * $y + $z);

        $a = 6378137.0;
        $f1 = 298.257223563;
        $aB = $f1 / ($f1 - 1);
        $p = sqrt($xn * $xn + $yn * $yn);
        $e2 = 1 - (1 - 1 / $f1) * (1 - 1 / $f1);
        $theta = atan($zn * $aB / $p);
        $st = sin($theta);
        $ct = cos($theta);
        $t = ($zn + $e2 * $aB * $a * $st * $st * $st) / ($p - $e2 * $a * $ct * $ct * $ct);
        $B = atan($t);
        $L = 2 * atan($yn / ($p + $xn));
        $hOut = sqrt(1 + $t * $t) * ($p - $a / sqrt(1 + (1 - $e2) * $t * $t));

        $lat = $B * 180 / M_PI;
        $long = $L * 180 / M_PI;

        $height = floor($hOut * 100) / 100;

        return array($lat, $long, $height);
    }

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run(): void {
        $allFiles = scandir('build/CSV/');
        $posts = $this->table('places');
        $data = [];
        foreach ($allFiles as $filePos => $dirInfo) {
            if (is_file('build/CSV/' . $dirInfo)) {
                echo " $filePos / " . count($allFiles) . " " . $dirInfo;
                foreach (file('build/CSV/' . $dirInfo) as $pos => $inputRowText) {
                    if ($pos != 0) { //Skip Header Row
                        $inputRow = str_getcsv(iconv('windows-1250', 'UTF-8',
                                        $inputRowText), ';');

                        $offset = 0;

                        $record = [
                            'adm' => intval($inputRow[$offset++]),
                            'code' => intval($inputRow[$offset++]),
                            'name' => $inputRow[$offset++],
                            'momc_code' => intval($inputRow[$offset++]),
                            'momc_name' => $inputRow[$offset++],
                            'mop_code' => intval($inputRow[$offset++]),
                            'mop_name' => $inputRow[$offset++],
                            'district_code' => $inputRow[$offset++],
                            'district_name' => $inputRow[$offset++],
                            'street_code' => intval($inputRow[$offset++]),
                            'street_name' => $inputRow[$offset++],
                            'so_type' => $inputRow[$offset++],
                            'house_number' => intval($inputRow[$offset++]),
                            'orientation_number' => intval($inputRow[$offset++]),
                            'orientation_number_mark' => $inputRow[$offset++],
                            'zip' => $inputRow[$offset++],
                            'y' => floatval($inputRow[$offset++]),
                            'x' => floatval($inputRow[$offset++]),
                        ];

                        if ($record['x'] != 0 && $record['y'] != 'y') {
                            $wgs84 = $this->tsktowgs84($record['x'], $record['y'], 200);
                            $record['latitude'] = $wgs84[0];
                            $record['longitude'] = $wgs84[1];
                        } else {
                            $record['latitude'] = 0;
                            $record['longitude'] = 0;
                        }

//                        print_r($record);
                        
                        $data[] = $record;

                        echo '.';
                    }
                }
                echo "\n";
                $posts->insert($data)->save();
                $data = [];
            }
        }
    }
}

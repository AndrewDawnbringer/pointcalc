<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class CalcController extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct () {
        // output: 470 (370 alappont + 100 többletpont)
        $this->exampleData = [
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervező informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'magyar nyelv és irodalom',
                    'tipus' => 'közép',
                    'eredmeny' => '70%',
                ],
                [
                    'nev' => 'történelem',
                    'tipus' => 'közép',
                    'eredmeny' => '80%',
                ],
                [
                    'nev' => 'matematika',
                    'tipus' => 'emelt',
                    'eredmeny' => '90%',
                ],
                [
                    'nev' => 'angol nyelv',
                    'tipus' => 'közép',
                    'eredmeny' => '94%',
                ],
                [
                    'nev' => 'informatika',
                    'tipus' => 'közép',
                    'eredmeny' => '95%',
                ],
            ],
            'tobbletpontok' => [
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'B2',
                    'nyelv' => 'angol',
                ],
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'C1',
                    'nyelv' => 'német',
                ],
            ],
        ];

        // output: 476 (376 alappont + 100 többletpont)
        $this->exampleData1 = [
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervező informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'magyar nyelv és irodalom',
                    'tipus' => 'közép',
                    'eredmeny' => '70%',
                ],
                [
                    'nev' => 'történelem',
                    'tipus' => 'közép',
                    'eredmeny' => '80%',
                ],
                [
                    'nev' => 'matematika',
                    'tipus' => 'emelt',
                    'eredmeny' => '90%',
                ],
                [
                    'nev' => 'angol nyelv',
                    'tipus' => 'közép',
                    'eredmeny' => '94%',
                ],
                [
                    'nev' => 'informatika',
                    'tipus' => 'közép',
                    'eredmeny' => '95%',
                ],
                [
                    'nev' => 'fizika',
                    'tipus' => 'közép',
                    'eredmeny' => '98%',
                ],
            ],
            'tobbletpontok' => [
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'B2',
                    'nyelv' => 'angol',
                ],
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'C1',
                    'nyelv' => 'német',
                ],
            ],
        ];

        // output: hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt
        $this->exampleData2 = [
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervező informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'matematika',
                    'tipus' => 'emelt',
                    'eredmeny' => '90%',
                ],
                [
                    'nev' => 'angol nyelv',
                    'tipus' => 'közép',
                    'eredmeny' => '94%',
                ],
                [
                    'nev' => 'informatika',
                    'tipus' => 'közép',
                    'eredmeny' => '95%',
                ],
            ],
            'tobbletpontok' => [
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'B2',
                    'nyelv' => 'angol',
                ],
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'C1',
                    'nyelv' => 'német',
                ],
            ],
        ];

        // output: hiba, nem lehetséges a pontszámítás a magyar nyelv és irodalom tárgyból elért 20% alatti eredmény miatt
        $this->exampleData3 = [
            'valasztott-szak' => [
                'egyetem' => 'ELTE',
                'kar' => 'IK',
                'szak' => 'Programtervező informatikus',
            ],
            'erettsegi-eredmenyek' => [
                [
                    'nev' => 'magyar nyelv és irodalom',
                    'tipus' => 'közép',
                    'eredmeny' => '15%',
                ],
                [
                    'nev' => 'történelem',
                    'tipus' => 'közép',
                    'eredmeny' => '80%',
                ],
                [
                    'nev' => 'matematika',
                    'tipus' => 'emelt',
                    'eredmeny' => '90%',
                ],
                [
                    'nev' => 'angol nyelv',
                    'tipus' => 'közép',
                    'eredmeny' => '94%',
                ],
                [
                    'nev' => 'informatika',
                    'tipus' => 'közép',
                    'eredmeny' => '95%',
                ],
            ],
            'tobbletpontok' => [
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'B2',
                    'nyelv' => 'angol',
                ],
                [
                    'kategoria' => 'Nyelvvizsga',
                    'tipus' => 'C1',
                    'nyelv' => 'német',
                ],
            ],
        ];
    }

    public function basePoints ($data) {

        if (empty($data) || !isset($data)) {
            return false;
        }

        $university = $data['valasztott-szak']['egyetem'];
        $faculty = $data['valasztott-szak']['kar'];
        $degree = $data['valasztott-szak']['szak'];

        if ($university == 'ELTE' && $faculty == "IK" && $degree == "Programtervező informatikus") {
            $required = 'matematika';
            $optional = ['biológia' => true,'fizika' => true,'informatika' => true,'kémia' => true];

            foreach ($data['erettsegi-eredmenyek'] as $key => $val) {

                if ($val['nev'] == $required && intval($val['eredmeny'] >= 20)) {
                    $requiredValue = intval($val['eredmeny']);
                    break;  
                } else {
                    continue;
                }
            }

            //A legjobban sikerült kötelező tárgy meghatározása

            //Minden eredmény ideiglenes tömbbe helyezése
            foreach ($data['erettsegi-eredmenyek'] as $key => $val) {
                $optionalValues[$val['nev']] = intval($val['eredmeny']);
            }

            //Csak az adott szakhoz tartozók kiszűrése

            //dump($optional, $optionalValues);
            
            foreach (array_keys($optionalValues) as $searchValue) {
                
                if (array_key_exists($searchValue,$optional)) {
                    //dump($optionalValues[$searchValue]);
                    $acceptedOptional[$searchValue] = $optionalValues[$searchValue];
                }

            }

            //dump($acceptedOptional);

            $bestOptionalValue = max($acceptedOptional);
            $bestOptionalName = array_keys($optionalValues, max($acceptedOptional))[0];

            //dump($bestOptionalValue,$bestOptionalName);

            //Alappontszám kiszámítása

            $basePoints = ($requiredValue + $bestOptionalValue) * 2;

            //dump($basePoints);

            return $basePoints;

        } else if ($university == 'PPKE' && $faculty == "BTK" && $degree == "Anglisztika") {
            $required = ['angol'];
            $requiredLevel = ['emelt'];
            $optional = ['francia','német','olasz','orosz','spanyol','történelem'];

        } else {
            return false;
        }

    }

    public function extraPoints ($data) {

        if (empty($data) || !isset($data)) {
            return false;
        }

        //Emelt szintű érettségik
        $numberOfHighLevels = 0;

        foreach ($data['erettsegi-eredmenyek'] as $key => $val) {

            if ($val['tipus'] == 'emelt') {
                $numberOfHighLevels++;
            } 
        }

        $extraPointsForHighLevels = 50 * $numberOfHighLevels;
        //dump($extraPointsForHighLevels);

        //Nyelvvizsgák
        //B2 28 pont
        //C1 40 pont

        $numberOfB2 = 0;
        $numberOfC1 = 0;

        foreach ($data['tobbletpontok'] as $key => $val) {

            if ($val['tipus'] == 'B2') {
                $numberOfB2++;
            } 

            if ($val['tipus'] == 'C1') {
                $numberOfC1++;
            } 
        }

        //dump($numberOfB2);
        //dump($numberOfC1);

        $extraPointsForB2 = 28 * $numberOfB2;
        $extraPointsForC1 = 40 * $numberOfC1;

        //dump($extraPointsForB2, $extraPointsForC1);

        $extraPointsTotal = $extraPointsForHighLevels + $extraPointsForB2 + $extraPointsForC1;

        if ($extraPointsTotal > 100) {
            return 100;
        } else {
            return $extraPointsTotal;
        }

    }

    public function totalPoints ($base,$extra) {

        if (empty($base) || !isset($base)) {
            return false;
        }

        if (empty($extra) || !isset($extra)) {
            return false;
        }

        return $base+$extra;
    }

    public function __invoke () {
       //$result = $this->basePoints($this->exampleData3);
       
       $basePoints = $this->basePoints($this->exampleData1);
       $extraPoints = $this->extraPoints($this->exampleData1);
       $totalPoints = $this->totalPoints($basePoints, $extraPoints);

       return view('pointcalc', [
        'basePoints' => $basePoints,
        'extraPoints' => $extraPoints,
        'totalPoints' => $totalPoints
        ]);
    }   
}



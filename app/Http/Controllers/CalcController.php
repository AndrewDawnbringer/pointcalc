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

            dump($optional, $optionalValues);
            
            foreach ($optionalValues as $searchValue) {
                dump(array_key_exists($searchValue, $optional));
            }

            $bestOptionalValue = max($optionalValues);
            $bestOptionalName = array_keys($optionalValues, max($optionalValues));

            //Alappontszám kiszámítása

            

        } else if ($university == 'PPKE' && $faculty == "BTK" && $degree == "Anglisztika") {
            $required = ['angol'];
            $requiredLevel = ['emelt'];
            $optional = ['francia','német','olasz','orosz','spanyol','történelem'];

        } else {
            return false;
        }

    }

    public function __invoke () {
       //$result = $this->basePoints($this->exampleData3);
       //return view('pointcalc', ['result' => $result]);
       $this->basePoints($this->exampleData3);
    }   
}



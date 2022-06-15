<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class CalcController extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct () {

        $this->app = app();

        // output: 470 (370 alappont + 100 többletpont)
        $this->exampleData0 = [
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
            $required = ['matematika' => true, 'magyar nyelv és irodalom' => true, 'történelem' => true];
            $optional = ['biológia' => true,'fizika' => true,'informatika' => true,'kémia' => true];
            $degreeRequired = 'matematika';

            /*
            foreach ($data['erettsegi-eredmenyek'] as $key => $val) {

                if ($val['nev'] == $required && intval($val['eredmeny'] >= 20)) {
                    $requiredValue = intval($val['eredmeny']);
                    break;  
                } else {
                    continue;
                }
            }
            */

            //Kötelező tárgyak meglétének ellenőrzése és azok > 20%
            foreach ($data['erettsegi-eredmenyek'] as $key => $val) {
                $requiredValues[$val['nev']] = intval($val['eredmeny']);
            }

            foreach (array_keys($requiredValues) as $searchValue) {
                
                if (array_key_exists($searchValue, $required)) {
                    $acceptedOptional[$searchValue] = $requiredValues[$searchValue];
                }

            }

            $intersect = array_intersect_key($required, $requiredValues);
            //dump($intersect);

            if (!isset($intersect['matematika'])) {
                //dump('Hiányzó tárgy: Matematika');
                return false;
            } else if (intval($requiredValues['matematika']) < 20) {
                //dump('Nem elégséges eredmény - Matematika: <20%');
                return false;
            } else {
                //dump('Matematika OK');
            }

            

            if (!isset($intersect['történelem'])) {
                //dump('Hiányzó tárgy: Történelem');
                return false;
            } else if (intval($requiredValues['történelem']) < 20) {
                //dump('Nem elégséges eredmény - Történelem: <20%');
                return false;
            } else {
                //dump('történelem OK');
            }
            
            

            if (!isset($intersect['magyar nyelv és irodalom'])) {
                //dump('Hiányzó tárgy: Magyar nyelv és irodalom');
                return false;
            } else if (intval($requiredValues['magyar nyelv és irodalom']) < 20) {
                //dump('Nem elégséges eredmény - Magyar nyelv és irodalom: <20%');
                return false;
            } else {
                //dump('magyar nyelv és irodalom OK');
            }
            
            

            //Kötelező pont kiszámítása

            foreach ($data['erettsegi-eredmenyek'] as $key => $val) {

                if ($val['nev'] == $degreeRequired && intval($val['eredmeny'] >= 20)) {
                    $requiredValue = intval($val['eredmeny']);
                    break;  
                } else {
                    continue;
                }
            }
            
            //dump($requiredValue);

            //A legjobban sikerült kötelező tárgy meghatározása

            //dump($optionalValues);

            //dump($data['erettsegi-eredmenyek']);

            //Minden eredmény ideiglenes tömbbe helyezése
            foreach ($data['erettsegi-eredmenyek'] as $key => $val) {
                $optionalValues[$val['nev']] = intval($val['eredmeny']);
                //dump($val['nev']);
            }

            //dump($optionalValues);
            
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

            //dump($acceptedOptional);
            //dump('max($acceptedOptional):'.max($acceptedOptional));
    

            //dump($bestOptionalValue,$bestOptionalName);

            //Alappontszám kiszámítása

            $basePoints = ($requiredValue + $bestOptionalValue) * 2;

            //dump('Required value:'.$requiredValue);
            //dump('Best optional value: '.$bestOptionalValue);
            //dump('Best optional name: '.$bestOptionalName);

            //dump($basePoints, $requiredValue, $bestOptionalValue);

            return $basePoints;

            //die();

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

        //Objektum létrehozása
        //$languageDegrees = $this->app->make('stdClass');

        foreach ($data['tobbletpontok'] as $key => $val) {

            switch ($val['tipus']) {
                case 'B2':
                    switch ($val['nyelv']) {
                        case 'angol':
                            $languageDegrees['english'] = 'B2';
                            break;
                        case 'német':
                            $languageDegrees['german'] = 'B2';
                            break;
                    }
                    break;
                case 'C1':
                    switch ($val['nyelv']) {
                        case 'angol':
                            $languageDegrees['english'] = 'C1';
                            break;
                        case 'német':
                            $languageDegrees['german'] = 'C1';
                            break;
                    }
                    break;
            }     
        }

        
        $numberOfB2 = 0;
        $numberOfC1 = 0;
        
        if ($languageDegrees['english'] == 'C1') {
            $numberOfC1++;
        } elseif ($languageDegrees['english'] == 'B2') {
            $numberOfB2++;
        }

        if ($languageDegrees['german'] == 'C1') {
            $numberOfC1++;
        } elseif ($languageDegrees['german'] == 'B2') {
            $numberOfB2++;
        }

        //dump($numberOfB2,$numberOfC1);

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

        //dump($base+$extra);

        return $base+$extra;
    }

    public function __invoke () {
    
    //0
        $basePoints0 = $this->basePoints($this->exampleData0);

        if ($basePoints0 == false) {
            $extraPoints0 = false;
            $totalPoints0 = false;
        } else {
            $extraPoints0 = $this->extraPoints($this->exampleData0);
            $totalPoints0 = $this->totalPoints($basePoints0, $extraPoints0);
        }

        //dump($basePoints0, $extraPoints0, $totalPoints0);
        
    //1
    $basePoints1 = $this->basePoints($this->exampleData1);

    if ($basePoints1 == false) {
        $extraPoints1 = false;
        $totalPoints1 = false;
    } else {
        $extraPoints1 = $this->extraPoints($this->exampleData1);
        $totalPoints1 = $this->totalPoints($basePoints1, $extraPoints1);
    }

    //2
    $basePoints2 = $this->basePoints($this->exampleData2);

    if ($basePoints2 == false) {
        $extraPoints2 = false;
        $totalPoints2 = false;
    } else {
        $extraPoints2 = $this->extraPoints($this->exampleData2);
        $totalPoints2 = $this->totalPoints($basePoints2, $extraPoints2);
    }

    //3
    $basePoints3 = $this->basePoints($this->exampleData3);

    if ($basePoints3 == false) {
        $extraPoints3 = false;
        $totalPoints3 = false;
    } else {
        $extraPoints3 = $this->extraPoints($this->exampleData3);
        $totalPoints3 = $this->totalPoints($basePoints3, $extraPoints3);
    }

       return view('pointcalc', [
        'basePoints0' => $basePoints0,
        'extraPoints0' => $extraPoints0,
        'totalPoints0' => $totalPoints0,

        'basePoints1' => $basePoints1,
        'extraPoints1' => $extraPoints1,
        'totalPoints1' => $totalPoints1,

        'basePoints2' => $basePoints2,
        'extraPoints2' => $extraPoints2,
        'totalPoints2' => $totalPoints2,

        'basePoints3' => $basePoints3,
        'extraPoints3' => $extraPoints3,
        'totalPoints3' => $totalPoints3,
        ]);
    }   
}



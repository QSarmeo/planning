<?php

declare(strict_types=1);
require_once dirname(__DIR__).'/vendor/autoload.php';

use App\MonthSummary;

$month = 05;
$year = 2024;

$input = [
    [
        'date' => '07/05',
        'duration' => 2
    ],
    [
        'date' => '15/05',
        'duration' => 7
    ],
    [
        'date' => '16/05',
        'duration' => 6
    ]
];

$monthSummary = new MonthSummary();
echo sprintf('Résultats pour le %s/%s', $month, $year);
var_dump($monthSummary->buildFromArray($month, $year, $input));

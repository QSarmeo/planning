<?php

namespace App\Tests;

use App\MonthSummary;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MonthSummaryTest extends TestCase
{
    /**
     * @param array{
     *      date: string,
     *      duration: int
     *  }[] $input
     *
     * @param array{
     *      regularDays: int,
     *      regularHours: int,
     *      sundayDays: int,
     *      sundayHours: int,
     *      holidayDays: int,
     *      holidayHours: int,
     *      awayDays: int,
     *  } $output
     */
    #[Test]
    #[DataProvider('basicExamples')]
    #[DataProvider('multipleDaysExamples')]
    #[DataProvider('advancedExamples')]
    #[DataProvider('edgeCasesExamples')]
    public function testBuildFromArray(array $input, array $output, string $rule): void
    {
        $month = 05;
        $year = 2024;

        $monthSummary = new MonthSummary();
        $this->assertEquals($output, $monthSummary->buildFromArray($month, $year, $input), $rule);
    }

    public static function basicExamples(): \Generator
    {
        yield 'Empty input' => [
            'input' => [],
            'output' => [
                'regularDays' => 0,
                'regularHours' => 0,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31,
            ],
            'rule' => 'Si aucune donnée en entrée, tout les compteurs doivent être à 0 sauf les jours d\'absence'
        ];

        yield 'Input with one workday' => [
            'input' => [
                [
                    'date' => '07/05',
                    'duration' => 2
                ]
            ],
            'output' => [
                'regularDays' => 1,
                'regularHours' => 2,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Une journée de 2h travaillée sur un jour standard'
        ];

        yield 'Input with one sunday workday' => [
            'input' => [
                [
                    'date' => '05/05', // Sunday
                    'duration' => 7
                ]
            ],
            'output' => [
                'regularDays' => 0,
                'regularHours' => 0,
                'sundayDays' => 1,
                'sundayHours' => 7,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Une journée de 7h travaillée sur un dimanche'
        ];

        yield 'Input with one holiday workday' => [
            'input' => [
                [
                    'date' => '01/05', // Holiday
                    'duration' => 7
                ]
            ],
            'output' => [
                'regularDays' => 0,
                'regularHours' => 0,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 1,
                'holidayHours' => 7,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Une journée de 7h travaillée sur un jour férié'
        ];
    }

    public static function multipleDaysExamples(): \Generator
    {
        yield 'Input with multiple workdays' => [
            'input' => [
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
            ],
            'output' => [
                'regularDays' => 3,
                'regularHours' => 15,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 3,
            ],
            'rule' => '3 jours distincts et un cumul total de 15h'
        ];

        yield 'Input with multiple workdays (regular, sunday, holidays)' => [
            'input' => [
                [
                    'date' => '01/05', // Holiday
                    'duration' => 5
                ],
                [
                    'date' => '05/05', // Sunday
                    'duration' => 7
                ],
                [
                    'date' => '08/05', // Holiday
                    'duration' => 5
                ],
                [
                    'date' => '12/05', // Sunday
                    'duration' => 7
                ],
                [
                    'date' => '16/05',
                    'duration' => 6
                ]
            ],
            'output' => [
                'regularDays' => 1,
                'regularHours' => 6,
                'sundayDays' => 2,
                'sundayHours' => 14,
                'holidayDays' => 2,
                'holidayHours' => 10,
                'awayDays' => 31 - 5,
            ],
            'rule' => '1 jour standard de 6h, 2 jours dimanche de 7h+7h et 2 jours fériés de 5h+5h'
        ];
    }

    public static function advancedExamples(): \Generator
    {
        yield 'Input with a holiday with too few hours' => [
            'input' => [
                [
                    'date' => '01/05', // Holiday
                    'duration' => 2
                ]
            ],
            'output' => [
                'regularDays' => 1,
                'regularHours' => 2,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Une journée de 2h travaillée sur un jour férié doit être considéré comme un jour standard'
        ];

        yield 'Input with a sunday with too few hours' => [
            'input' => [
                [
                    'date' => '05/05', // Sunday
                    'duration' => 2
                ]
            ],
            'output' => [
                'regularDays' => 1,
                'regularHours' => 2,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Une journée de 2h travaillée sur un dimanche doit être considéré comme un jour standard'
        ];

        yield 'Input with a sunday which is a holiday' => [
            'input' => [
                [
                    'date' => '26/05', // Sunday and holiday
                    'duration' => 6
                ]
            ],
            'output' => [
                'regularDays' => 0,
                'regularHours' => 0,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 1,
                'holidayHours' => 6,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Un dimanche férié est prioritairement férié avant d\'être un dimanche'
        ];

        yield 'Input with a sunday which is a holiday but with too few hours' => [
            'input' => [
                [
                    'date' => '26/05', // Sunday and holiday
                    'duration' => 2
                ]
            ],
            'output' => [
                'regularDays' => 1,
                'regularHours' => 2,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Un dimanche férié est un jour standard si pas assez d\'heures sont travaillées'
        ];

        yield 'Input with a sunday which is a holiday, with too few hours for a holiday but enough for a sunday' => [
            'input' => [
                [
                    'date' => '26/05', // Sunday and holiday
                    'duration' => 3
                ]
            ],
            'output' => [
                'regularDays' => 0,
                'regularHours' => 0,
                'sundayDays' => 1,
                'sundayHours' => 3,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Un dimanche férié compte comme un dimanche si le nombre d\'heures minimum pour un jour férié n\'est pas atteint'
        ];
    }

    public static function edgeCasesExamples(): \Generator
    {
        yield 'Working hours are limited per day' => [
            'input' => [
                [
                    'date' => '27/05',
                    'duration' => 15
                ]
            ],
            'output' => [
                'regularDays' => 1,
                'regularHours' => MonthSummary::MAX_WORKDAY_HOURS,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31 - 1,
            ],
            'rule' => 'Un jour travaillé a un nombre maximum d\'heures défini'
        ];

        yield 'Incorrect dates are ignored' => [
            'input' => [
                [
                    'date' => '27/04',
                    'duration' => 2
                ],
                [
                    'date' => '32/05',
                    'duration' => 2
                ],
                [
                    'date' => '-04/05',
                    'duration' => 2
                ],
                [
                    'date' => '24/13',
                    'duration' => 2
                ]
            ],
            'output' => [
                'regularDays' => 0,
                'regularHours' => 0,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31,
            ],
            'rule' => 'Les dates hors période ne sont pas prises en compte'
        ];

        yield 'Incorrect durations are ignored' => [
            'input' => [
                [
                    'date' => '27/05',
                    'duration' => -2
                ],
                [
                    'date' => '28/05',
                    'duration' => 0
                ]
            ],
            'output' => [
                'regularDays' => 0,
                'regularHours' => 0,
                'sundayDays' => 0,
                'sundayHours' => 0,
                'holidayDays' => 0,
                'holidayHours' => 0,
                'awayDays' => 31,
            ],
            'rule' => 'Les durées de travail négatives ou égales à 0 ne sont pas prises en compte'
        ];
    }
}

<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectPeriodChapterData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectPeriodCostData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectPeriodResponseData;

beforeEach(function (): void {
    config()->set('data', require dirname(__DIR__, 3).'/vendor/spatie/laravel-data/config/data.php');
});

it('maps a projectperiod.get response with costs and chapters', function (): void {
    $period = ProjectPeriodResponseData::from([
        'projectid' => 'PRH12JSV49ZRB39',
        'projectperiodid' => 'ST7FDY6WF71Q6RJ',
        'tasks' => [],
        'costs' => [
            [
                'id' => 'PCDD9FR5N25GZ29',
                'name' => 'DA25+',
                'chapterid' => 'FPF9RS1Y6XW27SV',
                'quantity' => 1,
                'price' => 385,
                'cost' => 155,
            ],
            [
                'id' => 'PC42DUFS7EH9UAJ',
                'name' => 'DA40+',
                'chapterid' => 'FPF9RS1Y6XW27SV',
                'quantity' => 1,
                'price' => 385,
                'cost' => 155,
            ],
        ],
        'chapters' => [
            [
                'id' => 'FPF9RS1Y6XW27SV',
                'name' => 'Blaat',
                'price' => 0,
            ],
        ],
    ]);

    expect($period->projectid)->toBe('PRH12JSV49ZRB39')
        ->and($period->projectperiodid)->toBe('ST7FDY6WF71Q6RJ')
        ->and($period->tasks)->toBe([])
        ->and($period->costs)->toHaveCount(2)
        ->and($period->costs[0])->toBeInstanceOf(ProjectPeriodCostData::class)
        ->and($period->costs[0]->id)->toBe('PCDD9FR5N25GZ29')
        ->and($period->costs[0]->name)->toBe('DA25+')
        ->and($period->costs[0]->cost)->toBe(155.0)
        ->and($period->costs[1]->id)->toBe('PC42DUFS7EH9UAJ')
        ->and($period->chapters[0])->toBeInstanceOf(ProjectPeriodChapterData::class)
        ->and($period->chapters[0]->name)->toBe('Blaat');
});

it('maps a projectperiod.get response without costs', function (): void {
    $period = ProjectPeriodResponseData::from([
        'projectid' => 'PR29B41K6TVNH42',
        'projectperiodid' => 'STKGAZNMY5WW62P',
        'tasks' => [],
        'costs' => [],
        'chapters' => [],
    ]);

    expect($period->costs)->toBe([])
        ->and($period->chapters)->toBe([]);
});

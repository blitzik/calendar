<?php

use Tester\Assert as Assert;

require '../../bootstrap.php';

$factory = new \blitzik\Calendar\HorizontalCalendarCellFactory();
$gen = new \blitzik\Calendar\CalendarGenerator($factory);

$data = $gen->getCalendarData(2015, 12);

Assert::count(49, $data);

$labelCells = [];
foreach ($data as $cell) {
    Assert::type('\blitzik\Calendar\ICell', $cell);

    if ($cell->isForLabel()) {
        $labelCells[] = $cell;
    }
}

Assert::count(7, $labelCells);
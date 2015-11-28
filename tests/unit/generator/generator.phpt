<?php

use Tester\Assert as Assert;

require '../../bootstrap.php';

$factory = new \blitzik\Calendar\HorizontalCalendarCellFactory();
$gen = new \blitzik\Calendar\CalendarGenerator($factory);

$data = $gen->getCalendarData(2015, 12);

$prevMonthDays = [];
$nextMonthDays = [];
$currMonthDays = []; // current

foreach ($data as $cell) {
    Assert::type('\blitzik\Calendar\ICell', $cell);

    $day = $cell->getDay();
    if ($day->isFromCurrentlyDisplayedMonth()) {
        $currMonthDays[] = $cell;
    }

    if ($day->isFromPreviousMonth()) {
        $prevMonthDays[] = $cell;
    }

    if ($day->isFromNextMonth()) {
        $nextMonthDays[] = $cell;
    }
}


Assert::count(2, $prevMonthDays);
Assert::count(31, $currMonthDays);
Assert::count(9, $nextMonthDays);



/////////////////////////////////////////


// Data generation with Vertical Calendar Cell Factory

$factory = new \blitzik\Calendar\VerticalCalendarCellFactory();
$gen = new \blitzik\Calendar\CalendarGenerator($factory);

$data = $gen->getCalendarData(2015, 12);

$prevMonthDays = [];
$nextMonthDays = [];
$currMonthDays = []; // current

foreach ($data as $cell) {
    Assert::type('\blitzik\Calendar\ICell', $cell);

    $day = $cell->getDay();
    if ($day->isFromCurrentlyDisplayedMonth()) {
        $currMonthDays[] = $cell;
    }

    if ($day->isFromPreviousMonth()) {
        $prevMonthDays[] = $cell;
    }

    if ($day->isFromNextMonth()) {
        $nextMonthDays[] = $cell;
    }
}


Assert::count(2, $prevMonthDays);
Assert::count(31, $currMonthDays);
Assert::count(9, $nextMonthDays);



//////////////////////////////////////////



$factory = new \blitzik\Calendar\HorizontalCalendarCellFactory(\blitzik\Calendar\Calendar::WEDNESDAY);
$gen = new \blitzik\Calendar\CalendarGenerator($factory);

$data = $gen->getCalendarData(2015, 12);

$prevMonthDays = [];
$nextMonthDays = [];
$currMonthDays = []; // current

foreach ($data as $cell) {
    Assert::type('\blitzik\Calendar\ICell', $cell);

    $day = $cell->getDay();
    if ($day->isFromCurrentlyDisplayedMonth()) {
        $currMonthDays[] = $cell;
    }

    if ($day->isFromPreviousMonth()) {
        $prevMonthDays[] = $cell;
    }

    if ($day->isFromNextMonth()) {
        $nextMonthDays[] = $cell;
    }
}


Assert::count(6, $prevMonthDays);
Assert::count(31, $currMonthDays);
Assert::count(5, $nextMonthDays);
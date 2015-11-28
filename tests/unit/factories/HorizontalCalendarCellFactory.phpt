<?php

use Tester\Assert as Assert;

require '../../bootstrap.php';


// Calendar starts with Sunday [0] === first day of the November 2015 (Sunday[0] too)

$factory = new \blitzik\Calendar\HorizontalCalendarCellFactory();
$factory->setPeriod(2015, 11);

$l = $factory->isForDayLabel(0, 0);
Assert::true($l);

$l = $factory->isForDayLabel(1, 0);
Assert::false($l);


//////////////////////////////////////////


$cell = $factory->createCell(1, 0);
Assert::same(1, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st November 2015
Assert::same(0, $day->getWeekDayNumber()); // Sunday



$cell = $factory->createCell(5, 2);
Assert::same(31, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st December 2015
Assert::same(2, $day->getWeekDayNumber()); // Tuesday



//////////////////////////////////////////



// Calendar starts with Wednesday [3] > first day of the November 2015 (Sunday [0])

$factory = new \blitzik\Calendar\HorizontalCalendarCellFactory(\blitzik\Calendar\Calendar::WEDNESDAY);
$factory->setPeriod(2015, 11);

$l = $factory->isForDayLabel(0, 0);
Assert::true($l);

$l = $factory->isForDayLabel(1, 0);
Assert::false($l);



$cell = $factory->createCell(1, 0);
Assert::same(-3, $cell->getNumber());

$day = $cell->getDay();
Assert::same(28, $day->getDay()); // 28th October 2015
Assert::same(3, $day->getWeekDayNumber()); // Wednesday



$cell = $factory->createCell(1, 4);
Assert::same(1, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st November 2015
Assert::same(0, $day->getWeekDayNumber()); // Sunday



$cell = $factory->createCell(5, 6);
Assert::same(31, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st December 2015
Assert::same(2, $day->getWeekDayNumber()); // Tuesday



//////////////////////////////////////////



// Calendar starts with Monday [1] < first day of the December 2015 (Tuesday [2])

$factory = new \blitzik\Calendar\HorizontalCalendarCellFactory(\blitzik\Calendar\Calendar::MONDAY);
$factory->setPeriod(2015, 12);

$l = $factory->isForDayLabel(0, 0);
Assert::true($l);

$l = $factory->isForDayLabel(1, 0);
Assert::false($l);



$cell = $factory->createCell(1, 0);
Assert::same(0, $cell->getNumber());

$day = $cell->getDay();
Assert::same(30, $day->getDay()); // 30th November 2015
Assert::same(1, $day->getWeekDayNumber()); // Monday



$cell = $factory->createCell(1, 1);
Assert::same(1, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st December 2015
Assert::same(2, $day->getWeekDayNumber()); // Tuesday



$cell = $factory->createCell(5, 4);
Assert::same(32, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st January 2016
Assert::same(5, $day->getWeekDayNumber()); // Friday
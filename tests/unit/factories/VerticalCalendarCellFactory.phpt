<?php

use Tester\Assert as Assert;

require '../../bootstrap.php';


// Calendar starts with Sunday [0]

$factory = new \blitzik\Calendar\VerticalCalendarCellFactory();
$factory->setPeriod(2015, 12);

$l = $factory->isForDayLabel(0, 1);
Assert::false($l);

$l = $factory->isForDayLabel(0, 0);
Assert::true($l);


//////////////////////////////////////////


$cell = $factory->createCell(0, 1);
Assert::same(-1, $cell->getNumber());

$day = $cell->getDay();
Assert::same(29, $day->getDay()); // 29th November 2015
Assert::same(0, $day->getWeekDayNumber()); // Sunday



$cell = $factory->createCell(2, 1);
Assert::same(1, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st December 2015
Assert::same(2, $day->getWeekDayNumber()); // Tuesday



$cell = $factory->createCell(4, 5);
Assert::same(31, $cell->getNumber());

$day = $cell->getDay();
Assert::same(31, $day->getDay()); // 31st December 2015
Assert::same(4, $day->getWeekDayNumber()); // Thursday



$cell = $factory->createCell(5, 5);
Assert::same(32, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st January 2016
Assert::same(5, $day->getWeekDayNumber()); // Friday



//////////////////////////////////////////


// Calendar starts with Wednesday [3]

$factory = new \blitzik\Calendar\VerticalCalendarCellFactory(\blitzik\Calendar\Calendar::WEDNESDAY);
$factory->setPeriod(2015, 12);

$l = $factory->isForDayLabel(0, 1);
Assert::false($l);

$l = $factory->isForDayLabel(0, 0);
Assert::true($l);



$cell = $factory->createCell(0, 1);
Assert::same(-5, $cell->getNumber());

$day = $cell->getDay();
Assert::same(25, $day->getDay()); // 25th November 2015
Assert::same(3, $day->getWeekDayNumber()); // Wednesday



$cell = $factory->createCell(6, 1);
Assert::same(1, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st December 2015
Assert::same(2, $day->getWeekDayNumber()); // Tuesday



$cell = $factory->createCell(1, 6);
Assert::same(31, $cell->getNumber());

$day = $cell->getDay();
Assert::same(31, $day->getDay()); // 31st December 2015
Assert::same(4, $day->getWeekDayNumber()); // Thursday



$cell = $factory->createCell(2, 6);
Assert::same(32, $cell->getNumber());

$day = $cell->getDay();
Assert::same(1, $day->getDay()); // 1st January 2016
Assert::same(5, $day->getWeekDayNumber()); // Friday
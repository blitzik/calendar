<?php

use Tester\Assert as Assert;

require '../../bootstrap.php';

$cell = new \blitzik\Calendar\Cell(-1, 2015, 12, false); // 29th November 2015
$day = $cell->getDay();
$day2 = $cell->getDay();

Assert::true($day === $day2);
Assert::true($day->isFromPreviousMonth());
Assert::false($day->isFromCurrentlyDisplayedMonth());
Assert::false($day->isFromNextMonth());



$cell = new \blitzik\Calendar\Cell(1, 2015, 12, false); // 1st December 2015
$day = $cell->getDay();

Assert::false($day->isFromPreviousMonth());
Assert::true($day->isFromCurrentlyDisplayedMonth());
Assert::false($day->isFromNextMonth());



$cell = new \blitzik\Calendar\Cell(32, 2015, 12, false); // 1st January 2016
$day = $cell->getDay();

Assert::false($day->isFromPreviousMonth());
Assert::false($day->isFromCurrentlyDisplayedMonth());
Assert::true($day->isFromNextMonth());



$cell = new \blitzik\Calendar\Cell(15, 2015, 12, true); // cell is for label
$day = $cell->getDay();

Assert::false($day->isFromPreviousMonth());
Assert::false($day->isFromCurrentlyDisplayedMonth());
Assert::false($day->isFromNextMonth());
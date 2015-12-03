<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Generator;

use blitzik\Calendar\Entities\ICell;

interface ICalendarGenerator
{
    /**
     * @param int $year
     * @param int $month
     * @return ICell[]
     */
    public function getCalendarData($year, $month);
}
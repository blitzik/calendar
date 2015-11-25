<?php

namespace blitzik\Calendar;

interface ICalendarGenerator
{
    /**
     * @param int $year
     * @param int $month
     * @return ICell[]
     */
    public function getCalendarData($year, $month);
}
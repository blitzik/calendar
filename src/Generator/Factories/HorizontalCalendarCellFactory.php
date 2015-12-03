<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Factories;

class HorizontalCalendarCellFactory extends CellFactory
{
    /**
     * @param int $row
     * @param int $col
     * @return bool
     */
    public function isForDayLabel($row, $col)
    {
        return $row === 0;
    }



    /**
     * @param int $row
     * @param int $col
     * @return int
     */
    public function calcNumber($row, $col)
    {
        return ($row - 1) * 7 - $this->getCalendarStartDay() + $col;
    }

}
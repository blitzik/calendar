<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Factories;

class VerticalCalendarCellFactory extends CellFactory
{
    /**
     * @param int $row
     * @param int $col
     * @return bool
     */
    public function isForDayLabel($row, $col)
    {
        return $col === 0;
    }

    

    /**
     * @param int $row
     * @param int $col
     * @return int
     */
    public function calcNumber($row, $col)
    {
        return ($col - 1) * 7 - $this->getCalendarStartDay() + $row;
    }

}
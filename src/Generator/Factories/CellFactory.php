<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Factories;

use blitzik\Calendar\Entities\ICell;
use blitzik\Calendar\Entities\Cell;
use Nette\Object;

abstract class CellFactory extends Object implements ICellFactory
{
    /** @var int */
    private $year;

    /** @var int */
    private $month;

    /** @var int */
    private $calendarStartDay;

    /** @var int */
    private $weekStartDay = 0;

    public function __construct($weekStartDay = 0)
    {
        $this->weekStartDay = $weekStartDay;
    }


    public function setPeriod($year, $month)
    {
        $this->year = $year;
        $this->month = $month;

        $d = \DateTime::createFromFormat('!Y-m', $year.'-'.$month);
        $this->calendarStartDay = $this->getCountStart((int) date('w', $d->getTimestamp()));
    }



    /**
     * @param int $row
     * @param int $col
     * @return ICell
     */
    public function createCell($row, $col)
    {
        return new Cell(
            $this->calcNumber($row, $col),
            $this->year,
            $this->month,
            $this->isForDayLabel($row, $col)
        );
    }



    /**
     * Returns "distance" between calendar start day and first day of the month
     *
     * @param int $firstWeekDayOfMonth numeric representation of the day of the week
     * @return int
     */
    private function getCountStart($firstWeekDayOfMonth)
    {
        $start = 0; // if they are equal
        if ($this->weekStartDay < $firstWeekDayOfMonth) {
            $start = $firstWeekDayOfMonth - $this->weekStartDay;
        }

        if ($this->weekStartDay > $firstWeekDayOfMonth) {
            $start = 7 - ($this->weekStartDay - $firstWeekDayOfMonth);
        }

        return $start - 1;
    }



    /**
     * @return int
     */
    public function getNumberOfRows()
    {
        return 7;
    }



    /**
     * @return int
     */
    public function getNumberOfColumns()
    {
        return 7;
    }



    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }



    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }



    /**
     * @return int
     */
    public function getCalendarStartDay()
    {
        return $this->calendarStartDay;
    }



    public function getWeekStartDay()
    {
        return $this->weekStartDay;
    }

}
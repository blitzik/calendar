<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Entities;

use Nette\Object;

class Day extends Object implements IDay
{
    /** @var ICell */
    private $cell;

    /** @var int */
    private $year;

    /** @var int */
    private $month;

    /** @var int */
    private $day;

    /** @var \DateTime */
    private $date;

    public function __construct(ICell $cell)
    {
        $this->cell = $cell;
        $this->date = $this->prepareDateForDay($cell);
    }



    /**
     * @return bool
     */
    public function isCurrent()
    {
        return $this->date == \DateTime::createFromFormat('!Y-m-d', date('Y-m-d'));
    }



    /**
     * @return ICell
     */
    public function getCell()
    {
        return $this->cell;
    }



    /**
     * @return int
     */
    public function getMonth()
    {
        if (!isset($this->month)) {
            $this->month = $this->date->format('n');
        }

        return (int) $this->month;
    }



    /**
     * @return int
     */
    public function getDay()
    {
        if (!isset($this->day)) {
            $this->day = $this->date->format('j');
        }

        return (int) $this->day;
    }



    /**
     * @return int
     */
    public function getYear()
    {
        if (!isset($this->year)) {
            $this->year = $this->date->format('Y');
        }

        return (int) $this->year;
    }



    /**
     * @return int
     */
    public function getWeekDayNumber()
    {
        return (int) $this->date->format('w');
    }



    public function getDateTime()
    {
        return $this->date;
    }



    /**
     * @param ICell $cell
     * @return \DateTime
     */
    private function prepareDateForDay(ICell $cell)
    {
        $d = \DateTime::createFromFormat('!Y-m', "{$cell->getYear()}-{$cell->getMonth()}");

        if ($cell->getNumber() <= 0) {
            $days = abs($cell->getNumber() - 1);
            $d->sub(new \DateInterval('P'.$days.'D'));

        } elseif ($cell->getNumber() > $this->cell->getNumberOfDaysInMonth()) {
            $days = $cell->getNumber() - 1;
            $d->add(new \DateInterval('P'.$days.'D'));

        } else { // 0 < $cellNumber <= $numberOfDays
            $days = $cell->getNumber();
            $d = new \DateTime($cell->getYear().'-'.$cell->getMonth().'-'.$days);
        }

        return $d;
    }



    public function __toString()
    {
        return (string) $this->getDay();
    }

}
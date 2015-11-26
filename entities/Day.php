<?php

namespace blitzik\Calendar;

use Nette\Object;

class Day extends Object implements IDay
{
    /** @var ICell */
    protected $cell;

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
        $this->date = $this->prepareDateForDay(
            $cell->getNumber(),
            $cell->getYear(),
            $cell->getMonth()
        );
    }



    public function isCurrent()
    {
        return $this->date == \DateTime::createFromFormat('!Y-m-d', date('Y-m-d'));
    }



    public function isFromCurrentlyDisplayedMonth()
    {
        return $this->getMonth() == $this->cell->getMonth();
    }



    /**
     * @return int
     */
    public function getMonth()
    {
        if (!isset($this->month)) {
            $this->month = $this->date->format('n');
        }

        return $this->month;
    }



    /**
     * @return int
     */
    public function getDay()
    {
        if (!isset($this->day)) {
            $this->day = $this->date->format('j');
        }

        return $this->day;
    }



    /**
     * @return int
     */
    public function getYear()
    {
        if (!isset($this->year)) {
            $this->year = $this->date->format('Y');
        }

        return $this->year;
    }



    public function getWeekDayNumber()
    {
        return $this->date->format('w');
    }



    public function getDateTime()
    {
        return $this->date;
    }



    /**
     * @param int $cellNumber
     * @param int $year
     * @param int $month
     * @return \DateTime
     */
    private function prepareDateForDay($cellNumber, $year, $month)
    {
        $d = \DateTime::createFromFormat('!Y-m', "$year-$month");
        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        if ($cellNumber <= 0) {
            $days = abs($cellNumber - 1);
            $d->sub(new \DateInterval('P'.$days.'D'));

        } elseif ($cellNumber > $numberOfDays) {
            $days = $cellNumber - 1;
            $d->add(new \DateInterval('P'.$days.'D'));

        } else { // 0 < $cellNumber <= $numberOfDays
            $days = $cellNumber;
            $d = new \DateTime($year.'-'.$month.'-'.$days);
        }

        return $d;
    }



    public function __toString()
    {
        return (string) $this->getDay();
    }

}
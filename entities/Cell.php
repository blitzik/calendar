<?php

namespace blitzik\Calendar;

use Nette\Object;

class Cell extends Object implements ICell
{
    /** @var int */
    private $number;

    /**
     * Year of currently displayed month.
     * May differ from year of the blitzik\Date\Day year.
     * @var int
     */
    private $year;

    /**
     * Numeric representation of currently displayed month.
     * May differ from numeric representation of the blitzik\Date\Day month
     * @var int
     */
    private $month;

    /** @var Day */
    private $day;

    /** @var bool */
    private $isLabelCell = false;

    /** @var bool */
    private $label;

    public function __construct($cellNumber, $year, $month, $isForLabel)
    {
        $this->number = $cellNumber;
        $this->year = $year;
        $this->month = $month;
        $this->isLabelCell = $isForLabel;
    }



    public function setLabel($label)
    {
        $this->label = $label;
    }



    public function getLabel()
    {
        return $this->label;
    }



    /**
     * @return bool
     */
    public function isForLabel()
    {
        return $this->isLabelCell;
    }



    /**
     * @return int
     */
    public function getNumber()
    {
        return (int) $this->number;
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
    public function getYear()
    {
        return $this->year;
    }



    /**
     * @return IDay
     */
    public function getDay()
    {
        if (!isset($this->day)) {
            $this->day = $this->createDay();
        }

        return $this->day;
    }



    /**
     * @return IDay
     */
    protected function createDay()
    {
        return new Day($this);
    }
}
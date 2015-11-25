<?php

namespace blitzik\Calendar;

interface IDay
{
    /**
     * @return bool
     */
    public function isCurrent();



    /**
     * @return int
     */
    public function getYear();



    /**
     * @return int
     */
    public function getMonth();



    /**
     * @return int
     */
    public function getDay();



    /**
     * @return int
     */
    public function getWeekDayNumber();

}
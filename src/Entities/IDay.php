<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Entities;

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
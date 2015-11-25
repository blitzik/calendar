<?php

namespace blitzik\Calendar;

interface ICell
{
    /**
     * @return bool
     */
    public function isForLabel();



    /**
     * @return int
     */
    public function getNumber();



    /**
     * @return IDay
     */
    public function getDay();
}
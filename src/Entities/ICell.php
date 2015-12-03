<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Entities;

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
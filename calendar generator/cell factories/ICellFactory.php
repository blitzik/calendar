<?php

namespace blitzik\Calendar;

interface ICellFactory
{
    /**
     * @param int $row
     * @param int $col
     * @return ICell
     */
    public function createCell($row, $col);



    /**
     * @param int $row
     * @param int $col
     * @return int
     */
    public function calcNumber($row, $col);



    /**
     * @param int $row
     * @param int $col
     * @return bool
     */
    public function isForDayLabel($row, $col);



    /**
     * @return int
     */
    public function getNumberOfRows();



    /**
     * @return int
     */
    public function getNumberOfColumns();
}
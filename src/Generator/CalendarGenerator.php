<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Generator;

use blitzik\Calendar\Factories\ICellFactory;
use Nette\SmartObject;

class CalendarGenerator implements ICalendarGenerator
{
    use SmartObject;

    /** @var ICellFactory  */
    private $cellFactory;


    public function __construct(
        ICellFactory $cellFactory
    ) {
        $this->cellFactory = $cellFactory;
    }



    /**
     * @param ICellFactory $cellFactory
     */
    public function setCellFactory(ICellFactory $cellFactory)
    {
        $this->cellFactory = $cellFactory;
    }



    /**
     * @return ICellFactory
     */
    public function getCellFactory()
    {
        return $this->cellFactory;
    }



    /**
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getCalendarData($year, $month)
    {
        return $this->generateCalendar($year, $month);
    }



    protected function generateCalendar($year, $month)
    {
        $this->cellFactory->setPeriod($year, $month);

        $calendarTable = [];
        for ($row = 0; $row < $this->cellFactory->getNumberOfRows(); $row++) {
            for ($col = 0; $col < $this->cellFactory->getNumberOfColumns(); $col++) {
                $cell = $this->cellFactory->createCell($row, $col);
                $calendarTable[$cell->getNumber()] = $cell;
            }
        }

        return $calendarTable;
    }

}
<?php

namespace blitzik\Calendar;

use Nette\Localization\ITranslator;
use Nette\Application\UI\Control;
use Nette\Utils\Validators;
use Nette\Utils\Strings;

class Calendar extends Control
{
    // days for setting up calendar start day
    const MONDAY   = 1, TUESDAY = 2, WEDNESDAY = 3;
    const THURSDAY = 4, FRIDAY  = 5, SATURDAY  = 6;
    const SUNDAY   = 0;

    protected $weekDays = [
        'sunday', 'monday', 'tuesday',
        'wednesday', 'thursday', 'friday',
        'saturday'
    ];

    protected $months = [
        'january', 'february', 'march',
        'april', 'may', 'june',
        'july', 'august', 'september',
        'october', 'november', 'december'
    ];

    protected $calendarControls = [
        'nextMonth' => '»',
        'prevMonth' => '«'
    ];

    /** @persistent */
    public $month;

    /** @persistent */
    public $year;

    /** @var ICellFactory */
    protected $cellFactory;

    /** @var CalendarGenerator */
    protected $calendarGenerator;

    /** @var  ITranslator */
    protected $translator;

    /** @var array */
    protected $calendarData = [];

    /** @var string */
    private $calendarBlocksTemplate;

    private $numberOfDaysLabelsCharactersToTruncate;

    public function __construct(
    ) {
        $this->month = date('n');
        $this->year = date('Y');

        $this->calendarBlocksTemplate = __DIR__ . '/calendarBlocks.latte';
    }

    public function setCalendarGenerator(ICalendarGenerator $generator)
    {
        $this->calendarGenerator = $generator;
        $this->cellFactory = $generator->getCellFactory();
    }



    public function setCalendarBlocksTemplate($path)
    {
        $this->calendarBlocksTemplate = $path;
    }



    public function setTranslator(ITranslator $translator)
    {
        $this->translator = $translator;
    }



    public function render()
    {
        $template = $this->getTemplate();
        $template->setFile($this->getTemplateFile());

        if (isset($this->translator)) {
            $template->setTranslator($this->translator);
        }

        if (!isset($this->calendarGenerator)) {
            $this->cellFactory = new HorizontalCalendarCellFactory();
            $this->calendarGenerator = new CalendarGenerator($this->cellFactory);
        }

        $this->calendarData = $this->getCalendarData();

        $this->prepareLabels();
        $this->prepareCellsLabels($this->calendarData);

        $template->calendarData = $this->calendarData;

        $template->monthName = $this->getMonthName($this->month);
        $template->month = $this->month;
        $template->year = $this->year;

        $template->rows = $this->cellFactory->getNumberOfRows();
        $template->cols = $this->cellFactory->getNumberOfColumns();

        $template->nextMonthControlLabel = $this->getMonthControlLabel('nextMonth');
        $template->previousMonthControlLabel = $this->getMonthControlLabel('prevMonth');

        $template->getCellNumber = function ($row, $col) {
            return $this->cellFactory->calcNumber($row, $col);
        };

        $template->calendarBlocksTemplate = $this->calendarBlocksTemplate;

        $template->render();
    }



    protected function getTemplateFile()
    {
        return __DIR__ . '/calendar.latte';
    }



    protected function getCalendarData()
    {
        if (empty($this->calendarData)) {
            $this->calendarData = $this->calendarGenerator->getCalendarData($this->year, $this->month);
        }

        return $this->calendarData;
    }



    protected function prepareCellsLabels(array $cells)
    {
        foreach ($cells as $cell) {
            if (!$cell instanceof ICell) {
                throw new \InvalidArgumentException('Members of $cells argument must be instances of ' .Cell::class);
            }

            if ($cell->isForLabel()) {
                $cell->setLabel($this->getWeekDayLabel($cell->getDay()->getWeekDayNumber()));
            }
        }
    }


    protected function prepareLabels()
    {
        $weekStartDay = $this->cellFactory->getWeekStartDay();
        $days = \array_splice($this->weekDays, 0, $weekStartDay);

        $this->weekDays = array_merge($this->weekDays, $days);

        $i = $weekStartDay;
        $d = $this->weekDays;
        $this->weekDays = [];
        foreach ($d as $day) {
            if ($i > 6) {
                $i = 0;
            }
            $this->weekDays[$i] = $day;
            $i++;
        }
    }


    /**
     * @param int $index
     * @return string
     */
    protected function getWeekDayLabel($index)
    {
        $label = $this->weekDays[$index];
        if (isset($this->translator)) {
            $label = $this->translator->translate($label);
        }

        return Strings::substring($label, 0, $this->numberOfDaysLabelsCharactersToTruncate);
    }



    /**
     * @param int $monthNumber
     * @return string
     */
    protected function getMonthName($monthNumber)
    {
        $monthNumber -= 1;

        $monthLabel = $this->months[$monthNumber];
        if (isset($this->translator)) {
            $monthLabel = $this->translator->translate($monthLabel);
        }

        return Strings::firstUpper($monthLabel);
    }



    /**
     * @param $placeholder
     * @return string
     */
    protected function getMonthControlLabel($placeholder)
    {
        if (isset($this->translator)) {
            return $this->translator->translate($placeholder);
        }

        return $this->calendarControls[$placeholder];
    }



    /*
     * ---------------------------
     * ----- MONTHS SHIFTING -----
     * ---------------------------
     */

    public function handleNextMonth()
    {
        $d = $this->getDatetime($this->month, $this->year)->modify('+1 month');
        $this->refreshState($d);
    }



    public function handlePreviousMonth()
    {
        $d = $this->getDatetime($this->month, $this->year)->modify('-1 month');
        $this->refreshState($d);
    }



    /**
     * @param \DateTime $d
     */
    protected function refreshState(\DateTime $d)
    {
        $this->month = $d->format('n');
        $this->year = $d->format('Y');

        if ($this->presenter->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }



    /*
     * -------------------------------
     * ----- COMPONENT SETTINGS ------
     * -------------------------------
     */


    /**
     * @param $numberOfCharacters
     * @throws \Nette\Utils\AssertionException
     */
    public function truncateDaysLabelsTo($numberOfCharacters)
    {
        Validators::assert($numberOfCharacters, 'numericint:1..');
        $this->numberOfDaysLabelsCharactersToTruncate = $numberOfCharacters;
    }



    /**
     * @param int $month Integer number between 1 and 12
     * @throws \Exception
     */
    public function setMonth($month)
    {
        Validators::assert($month, 'numericint:1..12');
        $this->month = $month;
    }



    /**
     * @param int $year
     * @throws \Exception
     */
    public function setYear($year)
    {
        $this->year = $year;
    }



    /**
     * @param array $daysLabels array with labels for days and keys that are Numeric representations of the days of the week [0 => Sunday, 1=> Monday etc...]
     */
    public function setWeekDaysLabels(array $daysLabels)
    {
        $this->weekDays = $this->prepareItems($daysLabels, 7);
    }



    public function setCalendarControls(array $calendarControls)
    {
        $newControls = array_intersect_key($calendarControls, $this->calendarControls);
        if (count($newControls) !== 2) {
            throw new NumberOfMembersException('
                Check array keys of argument $calendarControls.
                There should be exactly 2 items with keys "nextMonth" and "prevMonth"
            ');
        }

        $this->calendarControls = $calendarControls;
    }



    /**
     * @param array $months array with months where january starts with key 0, 1 => february etc...
     */
    public function setMonths(array $months)
    {
        $this->months = $this->prepareItems($months, 12);
    }



    /**
     * @param array $array
     * @param $expectedCount
     * @return array
     */
    private function prepareItems(array $array, $expectedCount)
    {
        $c = count($array);
        if ($c !== $expectedCount) {
            throw new NumberOfMembersException(
                'Expected '.$expectedCount.' items in argument $months.
                 Instead ' .$c. ' given.'
            );
        }

        $result = [];
        $i = 0;
        foreach ($array as $item) {
            $result[$i] = $item;
            $i++;
        }

        return $result;
    }



    /**
     * @param $month
     * @param $year
     * @return \DateTime
     * @throws \Exception
     */
    protected function getDatetime($month, $year)
    {
        $datetime = \DateTime::createFromFormat('!Y-m', date($year.'-'.$month));
        if ($datetime === false) {
            throw new \Exception('Check $year and $month arguments');
        }

        return $datetime;
    }



    /**
     * Loads state information
     * @param  array
     * @return void
     */
    public function loadState(array $params)
    {
        parent::loadState($params);

        if (!$this->presenter->isAjax() and isset($params['month']) and isset($params['year'])) {
            $datetime = \DateTime::createFromFormat('!Y-m', date($params['year'].'-'.$params['month']));
            if ($datetime === false) {
                $datetime = \DateTime::createFromFormat('!Y-m', date('Y-m'));
            }

            $this->month = $datetime->format('n');
            $this->year = $datetime->format('Y');
        }

    }

}
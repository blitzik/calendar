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

    /** @persistent */
    public $monthSelection;

    /** @persistent */
    public $yearSelection;

    /** @var ICellFactory */
    protected $cellFactory;

    /** @var CalendarGenerator */
    protected $calendarGenerator;

    /** @var  ITranslator */
    protected $translator;

    /** @var array */
    protected $calendarData = [];

    /** @var string */
    private $calendarBlocksTemplate = __DIR__ . '/calendarBlocks.latte';

    private $numberOfDaysLabelsCharactersToTruncate;
    private $areSelectionsActive = false;



    public function __construct()
    {
        $this->month = date('n');
        $this->year = date('Y');
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
        $this->months = $this->translateMonthNames($this->months);
        $this->calendarControls = $this->translateControlLabels($this->calendarControls);

        $template->calendarData = $this->calendarData;

        $template->calendarControls = $this->calendarControls;
        $template->monthName = $this->months[$this->month - 1];
        $template->months = $this->months;
        $template->month = $this->month;
        $template->year = $this->year;
        $template->areSelectionsActive = $this->areSelectionsActive;

        $template->rows = $this->cellFactory->getNumberOfRows();
        $template->cols = $this->cellFactory->getNumberOfColumns();

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
                throw new \InvalidArgumentException(
                    'Members of $cells argument must be instances of ' . Cell::class
                );
            }

            if ($cell->isForLabel()) {
                $cell->setLabel(
                    $this->getWeekDayLabel($cell->getDay()->getWeekDayNumber())
                );
            }
        }
    }



    private function prepareLabels()
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
     * @param array $monthNames
     * @return string
     */
    protected function translateMonthNames($monthNames)
    {
        if (isset($this->translator)) {
            $names = [];
            foreach ($monthNames as $key => $monthName) {
                $names[$key] = $this->translator->translate($monthName);
            }

            return $names;
        }

        return $monthNames;
    }



    /**
     * @param array $calendarControls
     * @return array
     */
    protected function translateControlLabels(array $calendarControls)
    {
        if (isset($this->translator)) {
            $labels = [];
            foreach ($calendarControls as $key => $control) {
                    $labels[$key] = $this->translator->translate($key);
            }
            return $labels;
        }

        return $calendarControls;
    }



    /*
     * ---------------------------
     * ----- MONTHS SHIFTING -----
     * ---------------------------
     */

    public function handleNextMonth()
    {
        $d = $this->getDatetime($this->month, $this->year)->modify('+1 month');
        $this->refreshMonthsShifting($d);
    }



    public function handlePreviousMonth()
    {
        $d = $this->getDatetime($this->month, $this->year)->modify('-1 month');
        $this->refreshMonthsShifting($d);
    }



    /**
     * @param \DateTime $d
     */
    protected function refreshMonthsShifting(\DateTime $d)
    {
        $this->month = $d->format('n');
        $this->year = $d->format('Y');

        $this->yearSelection = null;
        $this->monthSelection = null;

        $this->refresh();
    }



    /*
     * ---------------------------------------
     * ----- MONTHS AND YEARS SELECTIONS -----
     * ---------------------------------------
     */

    public function handleShowMonthSelection()
    {
        $this->monthSelection = true;
        $this->yearSelection = null;

        $this->refresh();
    }



    public function handleShowYearSelection()
    {
        $this->yearSelection = true;
        $this->monthSelection = null;

        $this->refresh();
    }



    public function handleSelection($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->yearSelection = null;
        $this->monthSelection = null;

        $this->refresh();
    }



    protected function refresh()
    {
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

    public function enableSelections()
    {
        $this->areSelectionsActive = true;
    }


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
        $this->calendarControls = array_merge($this->calendarControls, $calendarControls);
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

        $datetime = \DateTime::createFromFormat('!Y-m', date($this->year.'-'.$this->month));
        if ($datetime === false) {
            $datetime = \DateTime::createFromFormat('!Y-m', date('Y-m'));

            $this->month = $datetime->format('n');
            $this->year = $datetime->format('Y');
        }
    }

}

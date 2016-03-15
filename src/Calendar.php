<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar;

use blitzik\Calendar\Factories\HorizontalCalendarCellFactory;
use bitzik\Calendar\Exceptions\LocaleNotFoundException;
use blitzik\Calendar\Generator\ICalendarGenerator;
use blitzik\Calendar\Generator\CalendarGenerator;
use blitzik\Calendar\Locales\BasicTranslator;
use blitzik\Calendar\Factories\ICellFactory;
use Nette\Localization\ITranslator;
use Nette\Application\UI\Control;
use Nette\Utils\Validators;

class Calendar extends Control
{
    const LANG_CS = 'cs', LANG_SK = 'sk';

    // days for setting up calendar start day
    const MONDAY   = 1, TUESDAY = 2, WEDNESDAY = 3;
    const THURSDAY = 4, FRIDAY  = 5, SATURDAY  = 6;
    const SUNDAY   = 0;

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
    private $calendarBlocksTemplate;

    /** @var string */
    private $pathToDictionary;

    private $numberOfDaysLabelsCharactersToTruncate;
    private $areSelectionsActive = false;



    public function __construct($locale = 'en')
    {
        $this->month = date('n');
        $this->year = date('Y');

        $this->loadLocale($locale);
        $this->calendarBlocksTemplate = __DIR__ . '/calendarBlocks.latte';
        $this->translator = new BasicTranslator($this->pathToDictionary);
    }



    public function setTranslator(ITranslator $translator)
    {
        $this->translator = $translator;
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

        $template->month = $this->month;
        $template->year = $this->year;
        $template->areSelectionsActive = $this->areSelectionsActive;
        $template->charsToShortTo = $this->numberOfDaysLabelsCharactersToTruncate;

        $template->rows = $this->cellFactory->getNumberOfRows();
        $template->cols = $this->cellFactory->getNumberOfColumns();

        $template->getCell = function ($row, $col) {
            $cellNumber =  $this->cellFactory->calcNumber($row, $col);
            return $this->calendarData[$cellNumber];
        };

        $template->getMonthName = function ($monthNumber) {
            return $this->getMonthName($monthNumber);
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



    public function getMonthName($monthNumber)
    {
        Validators::assert($monthNumber, 'numericint:1..12');

        return strtolower(\DateTime::createFromFormat('!m', $monthNumber)->format('F'));
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



    public function setPathToDictionary($pathToDictionary)
    {
        $this->pathToDictionary = $pathToDictionary;
        if ($this->translator instanceof BasicTranslator) {
            $this->translator->setPathToDictionary($pathToDictionary);
        }
    }



    protected function loadLocale($locale)
    {
        $path = __DIR__ . '/Locales';
        switch ($locale) {
            case self::LANG_CS: $path .= '/calendar.cs_CZ.neon'; break;
            case self::LANG_SK: $path .= '/calendar.sk_SK.neon'; break;
            case 'en':  $path .= '/calendar.en_US.neon'; break;

            default: throw new LocaleNotFoundException('Component does NOT contain default locale "' . $locale . '"');
        }

        $this->pathToDictionary = $path;
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
        }

        $this->month = $datetime->format('n');
        $this->year = $datetime->format('Y');
    }

}

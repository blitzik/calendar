<?php

/**
 * @license MIT
 * @author  Ales Tichava
 */

namespace blitzik\Calendar\Locales;

use Nette\Localization\ITranslator;
use Nette\Localization\message;
use Nette\Localization\plural;
use Nette\Neon\Neon;
use Nette\Object;

class BasicTranslator extends Object implements ITranslator
{
    /** @var array */
    private $dictionary;

    public function __construct($pathToDictionary)
    {
        $this->setPathToDictionary($pathToDictionary);
    }



    public function setPathToDictionary($pathToDictionary)
    {
        $this->dictionary =  Neon::decode(file_get_contents($pathToDictionary));
    }



    /**
     * Translates the given string.
     * @param  string   message
     * @param  int      plural count
     * @return string
     */
    public function translate($message, $count = null)
    {
        if (isset($this->dictionary[$message])) {
            return $this->dictionary[$message];
        }

        return $message;
    }

}
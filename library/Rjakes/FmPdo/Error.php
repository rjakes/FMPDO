<?php
/**
 * @package Rjakes\FmPdo
 *
 * Copyright 2013-2015, Roger Jacques Consulting
 * See enclosed MIT license
 */
namespace Rjakes\FmPdo;

class Error
{
    /**
     * @var string
     */
    protected $message = null;

    /**
     * @var int
     */
    protected $code = null;

    public function __construct($message = null, $code = null, FmPdo $fmPdo = null)
    {
        $this->fmPdo = $fmPdo;
        $this->message = $message;
        $this->code = $code;
        // @TODO create logging functions
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        if ($this->message === null && $this->code !== null) {
            return $this->errorCodeToString();
        }
        return $this->message;
    }

    /**
     * @return null|int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @todo implement
     */
    private function errorCodeToString()
    {
        return 'Error code ' . $this->code;
        /**
        // Default to English.
        $lang = 'en';
        if ($this->fmPdo) {
            $lang = basename($this->fmPdo->getProperty('locale'));
        }

        static $strings = array();
        if (empty($strings[$lang])) {
            if (!@include_once dirname(__FILE__) . '/Error/' . $lang . '.php') {
                include_once dirname(__FILE__) . '/Error/en.php';
            }
            $strings[$lang] = $__FM_ERRORS;
        }

        if (isset($strings[$lang][$this->getCode()])) {
            return $strings[$lang][$this->getCode()];
        }

        return $strings[$lang][-1];
        */
    }

    /**
     * @todo implement
     */
    public function isValidationError()
    {
        return false;
    }
}

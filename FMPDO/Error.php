<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */

@require_once 'PEAR.php';
if (!class_exists('PEAR_Error')) {
    require_once 'PEAR.php';
}


class FMPDO_Error extends PEAR_Error
{

    var $_fmpdo;


    function FMPDO_Error($message = null, $code = null)
    {
        //$this->_fmpdo =& $fmpdo;
        parent::PEAR_Error($message, $code);

//TODO create logging functions
    }


    function getMessage()
    {
        if ($this->message === null && $this->getCode() !== null) {
            return $this->getErrorString();
        }
        return parent::getMessage();
    }


    function getErrorString()
    {
        // Default to English.
        $lang = basename($this->_fmpdo->getProperty('locale'));
        if (!$lang) {
            $lang = 'en';
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
    }

    /**

     */
    function isValidationError()
    {
        return false;
    }

}
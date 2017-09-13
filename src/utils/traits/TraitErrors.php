<?php

namespace WebComplete\core\utils\traits;

/**
 * Class TraitErrors
 */
trait TraitErrors
{

    protected $_errors = [];

    /**
     * @param $error
     * @param string $code
     */
    public function addError($error, $code = '_')
    {
        if(!isset($this->_errors[$code])) {
            $this->_errors[$code] = [];
        }
        $this->_errors[$code][] = $error;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function hasErrors($code = '_')
    {
        return count($this->getErrors($code)) > 0;
    }

    /**
     * @param string $code
     * @return array
     */
    public function getErrors($code = '_')
    {
        return isset($this->_errors[$code])
            ? $this->_errors[$code]
            : [];
    }

    /**
     * @param string $code
     */
    public function resetErrors($code = '_')
    {
        unset($this->_errors[$code]);
    }

}
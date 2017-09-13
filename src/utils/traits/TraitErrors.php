<?php

namespace WebComplete\core\utils\traits;

/**
 * Class TraitErrors
 */
trait TraitErrors
{

    protected $_errors = [];

    /**
     * @param string $code
     * @param $error
     */
    public function addError($code, $error)
    {
        if(!isset($this->_errors[$code])) {
            $this->_errors[$code] = [];
        }
        $this->_errors[$code][] = $error;
    }

    /**
     * @param null|string $code
     * @return bool
     */
    public function hasErrors($code = null)
    {
        return count($this->getErrors($code)) > 0;
    }

    /**
     * @param null|string $code
     * @return array
     */
    public function getErrors($code = null)
    {
        if($code) {
            return isset($this->_errors[$code])
                ? $this->_errors[$code]
                : [];
        }
        return $this->_errors;
    }

    /**
     * @return array
     */
    public function getFirstErrors()
    {
        $result = [];
        foreach ($this->getErrors() as $field => $errors) {
            if($errors) {
                $result[$field] = reset($errors);
            }
        }
        return $result;
    }

    /**
     * @param null|string $code
     */
    public function resetErrors($code = null)
    {
        if($code) {
            unset($this->_errors[$code]);
        }
        else {
            $this->_errors = [];
        }
    }

}
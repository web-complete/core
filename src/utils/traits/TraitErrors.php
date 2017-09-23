<?php

namespace WebComplete\core\utils\traits;

/**
 * Class TraitErrors
 */
trait TraitErrors
{
    protected $errors = [];

    /**
     * @param string $code
     * @param $error
     */
    public function addError($code, $error)
    {
        if (!isset($this->errors[$code])) {
            $this->errors[$code] = [];
        }
        $this->errors[$code][] = $error;
    }

    /**
     * @param null|string $code
     * @return bool
     */
    public function hasErrors($code = null): bool
    {
        return \count($this->getErrors($code)) > 0;
    }

    /**
     * @param null|string $code
     * @return array
     */
    public function getErrors($code = null): array
    {
        if ($code !== null) {
            return $this->errors[$code] ?? [];
        }
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getFirstErrors(): array
    {
        $result = [];
        foreach ($this->getErrors() as $field => $errors) {
            if ($errors) {
                $result[$field] = \reset($errors);
            }
        }
        return $result;
    }

    /**
     * @param null|string $code
     */
    public function resetErrors($code = null)
    {
        if ($code !== null) {
            unset($this->errors[$code]);
        } else {
            $this->errors = [];
        }
    }
}

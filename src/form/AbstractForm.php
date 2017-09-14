<?php

namespace WebComplete\core\form;


abstract class AbstractForm
{

    private $rules;
    private $filters;

    protected $data = [];
    protected $errors = [];
    protected $defaultError = 'error';

    /**
     * @var object
     */
    protected $filterFactory;

    /**
     * @var object
     */
    protected $validatorFactory;

    /**
     * @return array [[field, validator, params, message], ...]
     *
     * validator - is a string equals to method of ValidatorFactory, method of the form or callable.
     * Validator should be declared as ($value, $params) : bool
     *
     * example
     * ```
     * [
     *      ['name', 'string', ['min' => 2, 'max' => 50], 'Incorrect name'],
     *      ['email', 'email', [], 'Incorrect email'],
     *      [['name', 'email'], 'required', [], 'Field is required'],
     *      [['description', 'label'], 'safe'],
     *      ['price', 'methodValidator', [], 'Incorrect'],
     *      ['some', [SomeValidator::class, 'method'], ['customParam' => 100], 'Incorrect'],
     *      [['*'], 'regexp', ['match' => '/^[a-z]$/'], 'Field is required'],
     * ]
     * ```
     */
    abstract public function rules();

    /**
     * @return array [[field, filter, params], ...]
     *
     * filter - is a string equals to method of FilterFactory, method of the form or callable
     * Filter should be declared as ($value, $params) : mixed, and return filtered value
     *
     * example
     * ```
     * [
     *      ['*', 'trim'],
     *      ['*', 'purify', ['js' => true]],
     *      [['first_name', 'last_name'], 'capitalize'],
     *      ['email', 'replace', ['from' => 'email.com', 'to' => 'gmail.com']],
     *      ['content', 'stripTags'],
     * ]
     * ```
     *
     */
    abstract public function filters();

    /**
     * AbstractForm constructor.
     * @param null|array $rules
     * @param null|array $filters
     * @param null|object $filterFactory
     * @param null|object $validatorFactory
     */
    public function __construct(
        $rules = null,
        $filters = null,
        $filterFactory = null,
        $validatorFactory = null
    )
    {
        $this->filterFactory = $filterFactory;
        $this->validatorFactory = $validatorFactory;

        $this->rules = is_array($rules)
            ? array_merge($this->rules(), $rules)
            : $this->rules();

        $this->filters = is_array($filters)
            ? array_merge($this->filters(), $filters)
            : $this->filters();
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $this->filter($data);
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $definitions = $this->normalize($this->rules);

        $this->resetErrors();
        foreach ($this->getData() as $field => $value) {
            if(isset($definitions[$field])) {
                foreach ($definitions[$field] as $definition) {
                    $defName = array_shift($definition);
                    $defParams = array_merge([$value], array_shift($definition));
                    $defMessage = array_shift($definition) ?: $this->defaultError;

                    if(is_callable($defName)) {
                        $callable = $defName;
                    }
                    else if($this->filterFactory && method_exists($this->filterFactory, $defName)) {
                        $callable = [$this->filterFactory, $defName];
                    }
                    else {
                        $callable = [$this, $defName];
                    }

                    if(!call_user_func_array($callable, $defParams)) {
                        $this->addError($field, $defMessage);
                    }
                }
            }
        }

        return !$this->hasErrors();
    }

    /**
     * @param $field
     * @param $error
     */
    public function addError($field, $error)
    {
        if(!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $error;
    }

    /**
     * @param string|null $field
     * @return bool
     */
    public function hasErrors($field = null)
    {
        return count($this->getErrors($field)) > 0;
    }

    /**
     * @param string|null $field
     * @return array
     */
    public function getErrors($field = null)
    {
        if($field) {
            return isset($this->errors[$field])
                ? $this->errors[$field]
                : [];
        }
        return $this->errors;
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
     * @param $field
     * @return string|null
     */
    public function getFirstError($field)
    {
        return isset($this->errors[$field]) && $this->errors[$field]
            ? reset($this->errors)
            : null;
    }

    /**
     * @param string $field
     */
    public function resetErrors($field = null)
    {
        if($field) {
            unset($this->errors[$field]);
        }
        else {
            $this->errors = [];
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function filter(array $data)
    {
        $definitions = $this->normalize($this->filters);

        foreach ($data as $field => $value) {
            if(isset($definitions[$field])) {
                foreach ($definitions[$field] as $definition) {
                    $defName = array_shift($definition);
                    $defParams = array_merge([$value], array_shift($definition));

                    if(is_callable($defName)) {
                        $callable = $defName;
                    }
                    else if($this->filterFactory && method_exists($this->filterFactory, $defName)) {
                        $callable = [$this->filterFactory, $defName];
                    }
                    else {
                        $callable = [$this, $defName];
                    }
                    $data[$field] = call_user_func_array($callable, $defParams);
                }
            }
        }

        return $data;
    }

    /**
     * @param $definitions
     * @return array
     */
    private function normalize($definitions)
    {
        $normalized = [];
        foreach ($definitions as $definition) {
            $fields = array_shift($definition);
            $defName = array_shift($definition);
            $defParams = $definition ? array_shift($definition) : [];
            $defMessage = $definition ? array_shift($definition) : '';
            if(!is_array($fields)) {
                $fields = [$fields];
            }
            foreach ($fields as $field) {
                if(!isset($normalized[$field])) {
                    $normalized[$field] = [];
                }
                $normalized[$field][] = [$defName, $defParams, $defMessage];
            }
        }

        if(isset($normalized['*'])) {
            $commonDefinitions = $normalized['*'];
            unset($normalized['*']);
            foreach ($normalized as $field => $definitions) {
                $normalized[$field] = array_merge($definitions, $commonDefinitions);
            }
        }

        return $normalized;
    }

}
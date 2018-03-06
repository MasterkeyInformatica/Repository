<?php

namespace Masterkey\Repository;

use Illuminate\Validation\Factory as ValidatorFactory;
use Masterkey\Repository\Contracts\ValidatorContract;

/**
 * AbstractValidator
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   06/03/2018
 * @package Masterkey\Repository
 */
abstract class AbstractValidator implements ValidatorContract
{
    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @param   ValidatorFactory  $factory
     */
    public function __construct(ValidatorFactory $factory)
    {
        $this->validatorFactory = $factory;
    }

    /**
     * @return array
     */
    public abstract function rules() : array;

    /**
     * @return array
     */
    public function messages() : array
    {
        return [];
    }

    /**
     * @return array
     */
    public function customAttributes() : array
    {
        return [];
    }

    /**
     * @param   null $action
     * @return  array
     */
    public function getRules($action = null) : array
    {
        $rules = $this->rules();

        if ( isset($rules[$action]) ) {
            $rules = $rules[$action];
        }

        return $rules;
    }

    /**
     * @param   array  $data
     * @param   null  $action
     * @return  bool
     * @throws  \Illuminate\Validation\ValidationException
     */
    public function validate(array $data, $action = NULL)
    {
        $this->validatorFactory->validate(
            $data,
            $this->getRules($action),
            $this->messages(),
            $this->customAttributes()
        );

        return true;
    }
}
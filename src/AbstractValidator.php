<?php

namespace Masterkey\Repository;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory as ValidatorFactory;
use Masterkey\Repository\Contracts\ValidatorContract;
use ValidationException;

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
     * @var Request
     */
    protected $request;

    /**
     * @param   ValidatorFactory $factory
     * @param   Request  $request
     */
    public function __construct(ValidatorFactory $factory, Request $request)
    {
        $this->validatorFactory = $factory;
        $this->request = $request;
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
     * @throws  ValidationException
     */
    public function validate(array $data, $action = NULL)
    {
        $validator = $this->validatorFactory->make(
            $data,
            $this->getRules($action),
            $this->messages(),
            $this->customAttributes()
        );

        if ( $validator->fails() ) {
            throw (new ValidationException($validator))->setRequest($this->request);
        }

        return true;
    }
}
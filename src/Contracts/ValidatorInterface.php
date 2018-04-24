<?php

namespace Masterkey\Repository\Contracts;

/**
 * ValidatorContract
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 2.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Contracts
 */
interface ValidatorInterface
{
    const INSERT_RULES = 0;

    const UPDATE_RULES = 1;

    /**
     * @return array
     */
    public function rules() : array;

    /**
     * @return array
     */
    public function messages() : array;

    /**
     * @return array
     */
    public function customAttributes() : array;

    /**
     * @param   array  $data
     * @param   null|int  $action
     */
    public function validate(array $data, $action = null);

    /**
     * @param   null|int  $action
     * @return  array
     */
    public function getRules($action = null) : array;
}
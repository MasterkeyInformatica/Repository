<?php

namespace Masterkey\Repository\Contracts;

/**
 * ValidatorContract
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   06/03/2018
 * @package Masterkey\Repository\Contracts
 */
interface ValidatorContract
{
    const INSERT_RULES = 0;

    const UPDATE_RULES = 1;

    public function rules() : array;

    public function messages() : array;

    public function customAttributes() : array;

    public function validate(array $data, $action = null);

    public function getRules($action = null) : array;
}
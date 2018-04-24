<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * RepositoryContract
 *
 * Interface that rules repository classes
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  3.1.0
 * @since    05/03/2018
 * @package  Masterkey\Repository\Contracts
 */
interface RepositoryInterface
{
    public function getBuilder() : Builder;

    public function getFieldsSearchable();
}

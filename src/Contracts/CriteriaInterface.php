<?php

namespace Masterkey\Repository\Contracts;

use Illuminate\Support\Collection;
use Masterkey\Repository\AbstractCriteria;

/**
 * CriteriaContract
 *
 * Interface that rules using of Criteria
 *
 * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version  3.0.0
 * @since    24/04/2018
 * @package  Masterkey\Repository\Contracts
 */
interface CriteriaInterface
{
    public function skipCriteria(bool $status = true);

    public function getCriteria() : Collection;

    public function getByCriteria(AbstractCriteria $criteria);

    public function pushCriteria(AbstractCriteria $criteria);

    public function applyCriteria();
}

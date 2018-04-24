<?php

namespace Masterkey\Repository\Contracts;

use Masterkey\Repository\Criteria;

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
    /**
     * @param   bool $status
     * @return  $this
     */
    public function skipCriteria($status = true);

    /**
     * @return  mixed
     */
    public function getCriteria();

    /**
     * @param   Criteria  $criteria
     * @return  $this
     */
    public function getByCriteria(Criteria $criteria);

    /**
     * @param   Criteria  $criteria
     * @return  $this
     */
    public function pushCriteria(Criteria $criteria);

    /**
     * @return $this
     */
    public function applyCriteria();
}

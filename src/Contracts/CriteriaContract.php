<?php

    namespace Masterkey\Repository\Contracts;

    use Masterkey\Repository\Criteria;

    /**
     * CriteriaContract
     *
     * Interface that rules using of Criteria
     *
     * @author   Matheus Lopes Santos <fale_com_lopez@hotmail.com>
     * @version  1.0.0
     * @since    26/12/2016
     * @package  Masterkey\Repository\Contracts
     */
    interface CriteriaContract
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
        public function  applyCriteria();
    }

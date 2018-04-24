<?php

namespace Masterkey\Repository\Traits;
use Illuminate\Support\Collection;
use Masterkey\Repository\Criteria;

/**
 * NeedsBeCriteriable
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Traits
 */
trait NeedsBeCriteriable
{
    /**
     * @return  $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);

        return $this;
    }

    /**
     * @param   boolean  $status
     * @return  $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * @return   \Illuminate\Support\Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param   Criteria  $criteria
     * @return  Collection
     */
    public function getByCriteria(Criteria $criteria) : Collection
    {
        $this->pushCriteria($criteria);

        return $this->all();
    }

    /**
     * @param   Criteria  $criteria
     * @return  $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ( $this->preventCriteriaOverwriting ) {
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return ( is_object($item) && (get_class($item) == get_class($criteria)) );
            });

            // Remove old criteria
            if ( is_int($key) ) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * @return   $this
     */
    public function applyCriteria()
    {
        if ( $this->skipCriteria === true ) {
            return $this;
        }

        foreach ( $this->getCriteria() as $criteria ) {
            if ( $criteria instanceof Criteria ) {
                $this->model = $criteria->apply($this->model, $this);
            }
        }

        return $this;
    }
}
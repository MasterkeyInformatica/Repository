<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Query\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * Offset
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class Offset extends AbstractCriteria
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @param int $offset
     */
    public function __construct(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->offset($this->offset);
    }
}
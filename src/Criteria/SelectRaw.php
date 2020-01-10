<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Query\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

/**
 * SelectRaw
 *
 * @author Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package Masterkey\Repository\Criteria
 */
class SelectRaw extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $expression;

    /**
     * @var array
     */
    protected $bindings;

    /**
     * @param string $expression
     * @param array  $bindings
     */
    public function __construct(string $expression, array $bindings = [])
    {
        $this->expression = $expression;
        $this->bindings = $bindings;
    }

    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder|mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->selectRaw($this->expression, $this->bindings);
    }
}

<?php

namespace Masterkey\Repository\Criteria;

use Illuminate\Database\Query\Builder;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;

class WhereIn extends AbstractCriteria
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var mixed
     */
    protected $values;

    /**
     * @var string
     */
    protected $boolean;

    /**
     * @var bool
     */
    protected $not;

    /**
     * @param string $column
     * @param mixed  $values
     * @param string $boolean
     * @param bool   $not
     */
    public function __construct(string $column, $values, string $boolean = 'and', bool $not = false)
    {
        $this->column = $column;
        $this->values = $values;
        $this->boolean = $boolean;
        $this->not = $not;
    }

    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder
     */
    public function apply($model, Repository $repository)
    {
        return $model->whereIn(
            $this->column,
            $this->values,
            $this->boolean,
            $this->not
        );
    }
}
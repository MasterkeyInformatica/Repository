<?php

namespace Masterkey\Repository\Criteria;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Masterkey\Repository\AbstractCriteria;
use Masterkey\Repository\Contracts\RepositoryInterface;
use Masterkey\Repository\Contracts\RepositoryInterface as Repository;
use Masterkey\Repository\RepositoryException;

/**
 * RequestCriteria
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 3.0.0
 * @package Masterkey\Repository\Criteria
 */
class RequestCriteria extends AbstractCriteria
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder    $model
     * @param Repository $repository
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        $fieldsSearchable = $repository->getFieldsSearchable();
        $search           = $this->request->get(Config::get('repository.criteria.params.search', 'search'), null);
        $searchFields     = $this->request->get(Config::get('repository.criteria.params.searchFields', 'searchFields'), null);
        $filter           = $this->request->get(Config::get('repository.criteria.params.filter', 'filter'), null);
        $orderBy          = $this->request->get(Config::get('repository.criteria.params.orderBy', 'orderBy'), null);
        $sortedBy         = $this->request->get(Config::get('repository.criteria.params.sortedBy', 'sortedBy'), 'asc');
        $with             = $this->request->get(Config::get('repository.criteria.params.with', 'with'), null);
        $searchJoin       = $this->request->get(Config::get('repository.criteria.params.searchJoin', 'searchJoin'), null);
        $limit            = $this->request->get(Config::get('repository.criteria.params.limit', 'limit'), null);
        $sortedBy         = !empty($sortedBy) ? $sortedBy : 'asc';

        if ($search && is_array($fieldsSearchable) && count($fieldsSearchable)) {
            $searchFields       = is_array($searchFields) || is_null($searchFields) ? $searchFields : explode(';', $searchFields);
            $fields             = $this->parserFieldsSearch($fieldsSearchable, $searchFields);
            $isFirstField       = true;
            $searchData         = $this->parserSearchData($search);
            $search             = $this->parserSearchValue($search);
            $modelForceAndWhere = strtolower($searchJoin) === 'and';

            $model = $model->where(function ($query) use ($fields, $search, $searchData, $isFirstField, $modelForceAndWhere) {
                /** @var Builder $query */
                foreach ($fields as $field => $condition) {
                    if (is_numeric($field)) {
                        $field     = $condition;
                        $condition = "=";
                    }

                    $value     = null;
                    $condition = trim(strtolower($condition));

                    if (isset($searchData[$field])) {
                        $value = ($condition == "like" || $condition == "ilike") ? "%{$searchData[$field]}%" : $searchData[$field];
                    } else {
                        if (!is_null($search)) {
                            $value = ($condition == "like" || $condition == "ilike") ? "%{$search}%" : $search;
                        }
                    }

                    $relation = null;

                    if (stripos($field, '.')) {
                        $explode  = explode('.', $field);
                        $field    = array_pop($explode);
                        $relation = implode('.', $explode);
                    }

                    $modelTableName = $query->getModel()->getTable();

                    if ($isFirstField || $modelForceAndWhere) {
                        if (!is_null($value)) {
                            if (!is_null($relation)) {
                                $query->whereHas($relation, function ($query) use ($field, $condition, $value) {
                                    $query->where($field, $condition, $value);
                                });
                            } else {
                                $query->where($modelTableName . '.' . $field, $condition, $value);
                            }

                            $isFirstField = false;
                        }
                    } else {
                        if (!is_null($value)) {
                            if (!is_null($relation)) {
                                $query->orWhereHas($relation, function ($query) use ($field, $condition, $value) {
                                    $query->where($field, $condition, $value);
                                });
                            } else {
                                $query->orWhere($modelTableName . '.' . $field, $condition, $value);
                            }
                        }
                    }
                }
            });
        }

        if (isset($orderBy) && !empty($orderBy)) {
            $split = explode('|', $orderBy);

            if (count($split) > 1) {
                /*
                 * ex.
                 * products|description -> join products on current_table.product_id = products.id order by description
                 *
                 * products:custom_id|products.description -> join products on current_table.custom_id = products.id order
                 * by products.description (in case both tables have same column name)
                 */
                $table      = $model->getModel()->getTable();
                $sortTable  = $split[0];
                $sortColumn = $split[1];
                $split      = explode(':', $sortTable);

                if (count($split) > 1) {
                    $sortTable = $split[0];
                    $keyName   = $table . '.' . $split[1];
                } else {
                    /*
                     * If you do not define which column to use as a joining column on current table, it will
                     * use a singular of a join table appended with _id
                     *
                     * ex.
                     * products -> product_id
                     */
                    $prefix  = Str::singular($sortTable);
                    $keyName = $table . '.' . $prefix . '_id';
                }

                $model = $model
                    ->leftJoin($sortTable, $keyName, '=', $sortTable . '.id')
                    ->orderBy($sortColumn, $sortedBy)
                    ->addSelect($table . '.*');
            } else {
                $model = $model->orderBy($orderBy, $sortedBy);
            }
        }

        if (isset($filter) && !empty($filter)) {
            if (is_string($filter)) {
                $filter = explode(';', $filter);
            }

            $model = $model->select($filter);
        }

        if ($with) {
            $with  = explode(';', $with);
            $model = $model->with($with);
        }

        if ($limit) {
            $limit = (int)$limit;
            $model = $model->limit($limit);
        }

        return $model;
    }

    /**
     * @param array      $fields
     * @param array|null $searchFields
     * @return  array
     * @throws  RepositoryException
     */
    protected function parserFieldsSearch(array $fields = [], array $searchFields = null)
    {
        if (!is_null($searchFields) && count($searchFields)) {
            $acceptedConditions = Config::get('repository.criteria.acceptedConditions', [
                '=',
                'like',
            ]);

            $originalFields = $fields;
            $fields         = [];

            foreach ($searchFields as $index => $field) {
                $field_parts    = explode(':', $field);
                $temporaryIndex = array_search($field_parts[0], $originalFields);

                if (count($field_parts) == 2) {
                    if (in_array($field_parts[1], $acceptedConditions)) {
                        unset($originalFields[$temporaryIndex]);
                        $field     = $field_parts[0];
                        $condition = $field_parts[1];

                        $originalFields[$field] = $condition;
                        $searchFields[$index]   = $field;
                    }
                }
            }

            foreach ($originalFields as $field => $condition) {
                if (is_numeric($field)) {
                    $field     = $condition;
                    $condition = "=";
                }

                if (in_array($field, $searchFields)) {
                    $fields[$field] = $condition;
                }
            }

            if (count($fields) == 0) {
                throw new RepositoryException('Some Fields are not accepted. Verify you repository configuration: ' . implode(',', $searchFields));
            }
        }

        return $fields;
    }

    /**
     * @param mixed $search
     * @return  array
     */
    protected function parserSearchData($search)
    {
        $searchData = [];

        if (stripos($search, ':')) {
            $fields = explode(';', $search);

            foreach ($fields as $row) {
                try {
                    list($field, $value) = explode(':', $row);
                    $searchData[$field] = $value;
                } catch (Exception $e) {
                    //Surround offset error
                }
            }
        }

        return $searchData;
    }

    /**
     * @param mixed $search
     * @return  null
     */
    protected function parserSearchValue($search)
    {
        if (stripos($search, ';') || stripos($search, ':')) {
            $values = explode(';', $search);

            foreach ($values as $value) {
                $s = explode(':', $value);

                if (count($s) == 1) {
                    return $s[0];
                }
            }

            return null;
        }

        return $search;
    }
}
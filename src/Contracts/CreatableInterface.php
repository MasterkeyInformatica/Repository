<?php

namespace Masterkey\Repository\Contracts;


/**
 * CreatableInterface
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   24/04/2018
 * @package Masterkey\Repository\Contracts
 */
interface CreatableInterface
{
    /**
     * @param   array  $data
     */
    public function create(array $data);

    /**
     * @param   array  $data
     */
    public function firstOrCreate(array $data);

    /**
     * @param   array  $data
     */
    public function firstOrNew(array $data);

    /**
     * @param   array  $data
     */
    public function save(array $data);

    /**
     * @param   array  $data
     * @return  bool
     */
    public function insert(array $data) : bool;

    public function update(array $data, $id = null, string $attribute = null);

    /**
     * @param   array  $data
     */
    public function massUpdate(array $data);

    /**
     * @param   int  $id
     * @return  bool
     */
    public function delete(int $id) : bool;

    /**
     * @param   array  $records
     * @return  bool
     */
    public function destroy(array $records) : bool;

    /**
     * @param   int  $id
     * @param   string  $relation
     * @param   string  $attributes
     * @param   bool  $detach
     */
    public function sync($id, $relation, $attributes, $detach = true);
}
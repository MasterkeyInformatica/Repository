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
    public function create(array $data);

    public function firstOrCreate(array $data);

    public function firstOrNew(array $data);

    public function save(array $data);

    public function insert(array $data) : bool;

    public function update(array $data, int $id = null);

    public function massUpdate(array $data);

    public function delete(int $id) : bool;

    public function destroy(array $records) : bool;

    public function sync($id, $relation, $attributes, $detach = true);
}
<?php

namespace Masterkey\Repository\Events;

/**
 * EntityDeleted
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @since   13/03/2018
 * @package Masterkey\Repository\Events
 */
class EntityDeleted extends BaseRepositoryEvent
{
    protected string $action = 'delete';
}
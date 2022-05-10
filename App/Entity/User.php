<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * User
 *
 * @package App\Entity
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class User
{
    /** Primary key */
    public function __construct(public readonly int $id)
    {

    }

    public string $name;

    public ?string $sessionId;
}
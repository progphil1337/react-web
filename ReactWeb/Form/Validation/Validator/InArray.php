<?php

declare(strict_types=1);

namespace ReactWeb\Form\Validation\Validator;

use ReactWeb\Form\Validation\Validator;

/**
 * DefaultSelectValidValue
 *
 * @package ReactWeb\Form\Validation\Validator
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class InArray extends Validator
{

    /**
     * @param array<string> $allowedValues
     */
    public function __construct(private readonly array $allowedValues)
    {
        parent::__construct('inarray', null);
    }

    public function validate(mixed $value): bool
    {
        return in_array($value, $this->allowedValues);
    }

    public function getErrorMessage(mixed $value): string
    {
        return sprintf('%s is invalid. Valid values: %s', $value, implode(', ', $this->allowedValues));
    }
}
<?php

declare(strict_types=1);

namespace ReactWeb\Form\Validation\Validator;

use ReactWeb\Form\Validation\Validator;

/**
 * MinLength
 *
 * @package ReactWeb\Form\Validation\Validator
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class MinLength extends Validator
{

    public function __construct(int $min)
    {
        parent::__construct('minlength', $min, true);
    }

    public function validate(mixed $value): bool
    {
        $string = (string)$value;

        return strlen($string) >= $this->val;
    }

    public function getErrorMessage(mixed $value): string
    {
        return sprintf('Min length of %s characters not reached', $this->val);
    }
}
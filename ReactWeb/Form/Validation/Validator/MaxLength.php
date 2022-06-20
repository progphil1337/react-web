<?php

declare(strict_types=1);

namespace ReactWeb\Form\Validation\Validator;

use ReactWeb\Form\Validation\Validator;

/**
 * MaxLength
 *
 * @package ReactWeb\Form\Validation\Validator
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class MaxLength extends Validator
{
    public function __construct(int $max)
    {
        parent::__construct('maxlength', $max, true);
    }

    public function validate(mixed $value): bool
    {
        $string = (string)$value;

        return strlen($string) <= $this->val;
    }

    public function getErrorMessage(mixed $value): string
    {
        return sprintf('Max length of %s characters exceeded', $this->val);
    }
}
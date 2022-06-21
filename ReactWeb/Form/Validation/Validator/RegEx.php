<?php

declare(strict_types=1);

namespace ReactWeb\Form\Validation\Validator;

use ReactWeb\Form\Validation\Validator;

/**
 * RegEx
 *
 * @package ReactWeb\Form\Validation\Validator
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class RegEx extends Validator
{

    public function __construct(private readonly mixed $pattern)
    {
        parent::__construct('regex', $this->pattern);
    }

    public function validate(mixed $value): bool
    {
        return preg_match($this->pattern, $value) !== false;
    }

    public function getErrorMessage(mixed $value): string
    {
        return sprintf('%s does not match the pattern %s', $value, $this->pattern);
    }
}
<?php

declare(strict_types=1);

namespace ReactWeb\Form\Validation\Validator;

use ReactWeb\Form\Validation\Validator;

/**
 * Mail
 *
 * @package ReactWeb\Form\Validation\Validator
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class Mail extends Validator
{

    public function __construct()
    {
        parent::__construct('mail', null, false);
    }

    public function validate(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function getErrorMessage(mixed $value): string
    {
        return sprintf('%s is not a valid email address', $value);
    }
}
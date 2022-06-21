<?php

declare(strict_types=1);

namespace ReactWeb\Form\Validation;

use ReactWeb\Form\Input;

/**
 * Result
 *
 * @package ReactWeb\Form\Validation
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Result
{
    /** @var array<Input, array<\ReactWeb\Form\Validation\Validator> */
    private array $errorMessages = [];

    public function addErrorMessage(Input $input, Validator $validator, string $message): self
    {
        if (!array_key_exists($input->name, $this->errorMessages)) {
            $this->errorMessages[$input->name] = [];
        }

        $this->errorMessages[$input->name][] = [
            'message' => $message,
            'validator' => $validator
        ];

        return $this;
    }

    public function isValid(): bool
    {
        return count($this->errorMessages) === 0;
    }

    public function getByInput(Input $input): array
    {
        return $this->errorMessages[$input->name] ?? [];
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class EmailNotExists extends Constraint
{
    public string $message = "Cette adresse email est déjà utilisée.";

    public ?string $except = null;

    public function __construct(
        $options = null,
        array $groups = null,
        $payload = null,
        string $message = null,
        string $except = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->except = $except;
        $this->message = $message ?? $this->message;
    }
}

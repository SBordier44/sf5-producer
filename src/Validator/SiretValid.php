<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class SiretValid extends Constraint
{
    public string $message = 'Le numéro de Siret "{{ value }}" n\'est pas valide.';
}

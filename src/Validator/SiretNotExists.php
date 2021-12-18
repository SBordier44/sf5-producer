<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class SiretNotExists extends Constraint
{
    public string $message = "Ce numéro de siret est déjà enregistré chez nous.";
}

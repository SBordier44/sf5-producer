<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\FarmRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SiretNotExistsValidator extends ConstraintValidator
{
    public function __construct(private FarmRepository $farmRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($this->farmRepository->count(['siret' => $value]) === 0) {
            return;
        }

        /*** @var SiretNotExists $constraint */
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}

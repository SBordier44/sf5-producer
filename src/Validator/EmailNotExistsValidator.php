<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailNotExistsValidator extends ConstraintValidator
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var EmailNotExists $constraint */

        if (!$this->userRepository->count(['email' => $value])) {
            return;
        }

        if ($constraint->except === $value) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}

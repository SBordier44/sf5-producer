<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailExistsValidator extends ConstraintValidator
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($this->userRepository->count(['email' => $value]) > 0) {
            return;
        }

        /*** @var EmailExists $constraint */
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}

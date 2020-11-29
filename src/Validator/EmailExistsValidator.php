<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailExistsValidator extends ConstraintValidator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value === null || $value === '' || $this->userRepository->count(['email' => $value]) > 0) {
            return;
        }
        /*** @var EmailExists $constraint */
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}

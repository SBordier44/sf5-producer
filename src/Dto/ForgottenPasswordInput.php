<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator\EmailExists;
use Symfony\Component\Validator\Constraints as Assert;

class ForgottenPasswordInput
{
    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @EmailExists
     */
    private string $email = '';

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return ForgottenPasswordInput
     */
    public function setEmail(string $email): ForgottenPasswordInput
    {
        $this->email = $email;
        return $this;
    }
}

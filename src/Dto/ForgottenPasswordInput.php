<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\EmailExists;

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

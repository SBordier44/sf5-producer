<?php

declare(strict_types=1);

namespace App\Dto;

class FacilitySiretDto
{
    private ?string $siret = null;

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): FacilitySiretDto
    {
        $this->siret = $siret;
        return $this;
    }
}

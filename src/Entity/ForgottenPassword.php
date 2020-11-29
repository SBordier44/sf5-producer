<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Class FogottenPassword
 * @package App\Entity
 * @ORM\Embeddable()
 */
class ForgottenPassword
{
    /**
     * @ORM\Column(type="uuid", unique=true, nullable=true)
     */
    private ?Uuid $token;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $requestedAt;

    public function __construct()
    {
        $this->token = Uuid::v4();
        $this->requestedAt = new DateTimeImmutable();
    }

    /**
     * @return Uuid|null
     */
    public function getToken(): ?Uuid
    {
        return $this->token;
    }

    /**
     * @param Uuid $token
     * @return ForgottenPassword
     */
    public function setToken(Uuid $token): ForgottenPassword
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getRequestedAt(): ?DateTimeImmutable
    {
        return $this->requestedAt;
    }

    /**
     * @param DateTimeImmutable $requestedAt
     * @return ForgottenPassword
     */
    public function setRequestedAt(DateTimeImmutable $requestedAt): ForgottenPassword
    {
        $this->requestedAt = $requestedAt;
        return $this;
    }
}

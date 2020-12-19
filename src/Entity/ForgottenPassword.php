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
     * @var mixed $token
     */
    private $token;

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
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return ForgottenPassword
     * @codeCoverageIgnore
     */
    public function setToken($token): ForgottenPassword
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     * @codeCoverageIgnore
     */
    public function getRequestedAt(): ?DateTimeImmutable
    {
        return $this->requestedAt;
    }

    /**
     * @param DateTimeImmutable $requestedAt
     * @return ForgottenPassword
     * @codeCoverageIgnore
     */
    public function setRequestedAt(DateTimeImmutable $requestedAt): ForgottenPassword
    {
        $this->requestedAt = $requestedAt;
        return $this;
    }
}

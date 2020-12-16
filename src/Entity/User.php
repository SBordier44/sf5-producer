<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package App\Entity
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"producer"="App\Entity\Producer", "customer"="App\Entity\Customer"})
 * @UniqueEntity("email")
 */
abstract class User implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    protected Uuid $id;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     */
    protected string $firstName = '';

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     */
    protected string $lastName = '';

    /**
     * @ORM\Column(unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected string $email = '';

    /**
     * @Assert\NotBlank(groups={"password"})
     * @Assert\Length(min=8, groups={"password"})
     */
    protected ?string $plainPassword = null;

    /**
     * @ORM\Column
     */
    protected string $password = '';

    /**
     * @ORM\Embedded(class="ForgottenPassword")
     */
    protected ?ForgottenPassword $forgottenPassword;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected DateTimeImmutable $registeredAt;

    public function __construct()
    {
        $this->registeredAt = new DateTimeImmutable();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @param Uuid $id
     * @return User
     */
    public function setId(Uuid $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->forgottenPassword = null;
        $this->password = $password;
        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRegisteredAt(): DateTimeImmutable
    {
        return $this->registeredAt;
    }

    /**
     * @param DateTimeImmutable $registeredAt
     * @return User
     */
    public function setRegisteredAt(DateTimeImmutable $registeredAt): User
    {
        $this->registeredAt = $registeredAt;
        return $this;
    }

    public function getSalt()
    {
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function hasForgotHisPassword(): void
    {
        $this->forgottenPassword = new ForgottenPassword();
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    /**
     * @return ForgottenPassword|null
     */
    public function getForgottenPassword(): ?ForgottenPassword
    {
        return $this->forgottenPassword;
    }

    /**
     * @param ForgottenPassword|null $forgottenPassword
     * @return User
     */
    public function setForgottenPassword(?ForgottenPassword $forgottenPassword): User
    {
        $this->forgottenPassword = $forgottenPassword;
        return $this;
    }

    public function serialize(): string
    {
        return serialize(
            [
                $this->id,
                $this->email
            ]
        );
    }

    public function unserialize($serialized): array
    {
        return [
            $this->id,
            $this->email
        ] = unserialize($serialized, ['allowed_classes' => true]);
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $user->getUsername() === $this->getUsername();
    }
}

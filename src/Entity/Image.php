<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Image
{
    #[ORM\Column(name: 'image_path', type: Types::STRING, nullable: true)]
    private ?string $path = null;

    #[Assert\Image]
    private ?UploadedFile $file = null;

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): Image
    {
        $this->path = $path;
        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): Image
    {
        $this->file = $file;
        return $this;
    }
}

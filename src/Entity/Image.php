<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Image
 * @package App\Entity
 * @ORM\Embeddable
 */
class Image
{
    /**
     * @var string|null
     * @ORM\Column(name="image_path", nullable=true)
     */
    private ?string $path = null;

    /**
     * @var UploadedFile|null
     * @Assert\Image
     */
    private ?UploadedFile $file = null;

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return Image
     */
    public function setPath(?string $path): Image
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     * @return Image
     */
    public function setFile(?UploadedFile $file): Image
    {
        $this->file = $file;
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Farm;
use Symfony\Component\Uid\Uuid;

class FarmListener
{
    private string $uploadWebDir;
    private string $uploadAbsoluteDir;

    /**
     * ProductListener constructor.
     * @param string $uploadWebDir
     * @param string $uploadAbsoluteDir
     */
    public function __construct(string $uploadWebDir, string $uploadAbsoluteDir)
    {
        $this->uploadWebDir = $uploadWebDir;
        $this->uploadAbsoluteDir = $uploadAbsoluteDir;
    }

    public function preUpdate(Farm $farm): void
    {
        $this->upload($farm);
    }

    private function upload(Farm $farm): void
    {
        if ($farm->getImage() === null || $farm->getImage()->getFile() === null) {
            return;
        }

        $filename = Uuid::v4() . '.' . $farm->getImage()->getFile()->getClientOriginalExtension();

        $farm->getImage()->getFile()->move($this->uploadAbsoluteDir, $filename);

        $farm->getImage()->setPath($this->uploadWebDir . $filename);
    }
}

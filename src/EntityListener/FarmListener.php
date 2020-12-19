<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Farm;
use App\Repository\FarmRepository;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

class FarmListener
{
    private string $uploadWebDir;
    private string $uploadAbsoluteDir;
    private SluggerInterface $slugger;
    /**
     * @var FarmRepository
     */
    private FarmRepository $farmRepository;

    /**
     * FarmListener constructor.
     * @param SluggerInterface $slugger
     * @param FarmRepository $farmRepository
     * @param string $uploadWebDir
     * @param string $uploadAbsoluteDir
     */
    public function __construct(
        SluggerInterface $slugger,
        FarmRepository $farmRepository,
        string $uploadWebDir,
        string $uploadAbsoluteDir
    ) {
        $this->uploadWebDir = $uploadWebDir;
        $this->uploadAbsoluteDir = $uploadAbsoluteDir;
        $this->slugger = $slugger;
        $this->farmRepository = $farmRepository;
    }

    public function preUpdate(Farm $farm, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('name')) {
            $this->setSlug($farm);
        }
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

    public function prePersist(Farm $farm): void
    {
        $this->setSlug($farm);
    }

    private function setSlug(Farm $farm): void
    {
        $slug = $this->farmRepository->getNextSlug($this->slugger->slug($farm->getName())->lower()->toString());
        $farm->setSlug($slug);
    }
}

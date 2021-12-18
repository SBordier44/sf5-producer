<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Farm;
use App\Repository\FarmRepository;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class FarmListener
{
    public function __construct(
        private SluggerInterface $slugger,
        private FarmRepository $farmRepository
    ) {
    }

    public function preUpdate(Farm $farm, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('name')) {
            $this->setSlug($farm);
        }
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

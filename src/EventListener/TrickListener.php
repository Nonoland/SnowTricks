<?php

namespace App\EventListener;

use App\Entity\Trick;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickListener
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #[ORM\PrePersist]
    public function prePersist(LifecycleEventArgs $args): void
    {
        $trick = $args->getObject();

        if (empty($trick->getSlug())) {
            $slug = $this->slugger->slug($trick->getTitle())->lower();
            $trick->setSlug($this->makeSlugUnique($slug, $args));
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->prePersist($args);
    }

    private function makeSlugUnique($slug, LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();
        $repository = $em->getRepository(Trick::class);

        $originalSlug = $slug;
        $counter = 1;

        while ($repository->findOneBy(['slug' => $slug])) {
            $slug = $originalSlug . '_' . $counter;
            $counter++;
        }

        return $slug;
    }

}
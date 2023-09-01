<?php

namespace App\EventListener;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Date;

class CommentListener
{
    #[ORM\PrePersist]
    public function prePersist(LifecycleEventArgs $args): void
    {
        /** @var Comment $comment */
        $comment = $args->getObject();
        $comment->setDateAdd(new \DateTime());
    }
}
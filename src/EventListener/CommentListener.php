<?php

namespace App\EventListener;

use App\Entity\Comment;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CommentListener
{
    #[ORM\PrePersist]
    public function prePersist(LifecycleEventArgs $args): void
    {
        /** @var Comment $comment */
        $comment = $args->getObject();
        if (!$comment instanceof Comment) {
            return;
        }

        $comment->setDateAdd(new \DateTime());
    }
}
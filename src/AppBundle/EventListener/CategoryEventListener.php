<?php

namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Cocur\Slugify\Slugify;
use AppBundle\Entity\Category;

class CategoryEventListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Category) {
            if ($entity instanceof Category) {
                $this->slugifyName($entity);
            }
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Category) {
            $this->slugifyName($entity);
        }
    }

    /**
     * @param Category $entity
     * @return void
     */
    protected function slugifyName(Category $entity): void
    {
        if (empty($entity->getName())) {
            $entity->setName((new Slugify())->slugify($entity->getTitle()));
        }
    }
}
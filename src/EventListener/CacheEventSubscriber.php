<?php

namespace App\EventListener;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CacheEventSubscriber implements EventSubscriber
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $helper;

    public function __construct(
        CacheManager $cacheManager,
        UploaderHelper $helper
    )
    {
        $this->cacheManager = $cacheManager;
        $this->helper = $helper;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        /** @var Post|mixed $entity */
        $entity = $args->getEntity();
        if (!$entity instanceof Post && !$entity instanceof User) {
            return;
        }
        $this->cacheManager->remove($this->helper->asset($entity, 'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        /** @var Post|mixed $entity */
        $entity = $args->getEntity();
        if (!$entity instanceof Post && !$entity instanceof User) {
            return;
        }

        if ($entity->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->helper->asset($entity, 'imageFile'));
        }
    }

}

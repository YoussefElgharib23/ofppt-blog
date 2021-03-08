<?php

namespace App\EventListener;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class CacheEventSubscriber
 * @package App\EventListener
 */
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

    /**
     * CacheEventSubscriber constructor.
     * @param CacheManager $cacheManager
     * @param UploaderHelper $helper
     */
    public function __construct(
        CacheManager $cacheManager,
        UploaderHelper $helper
    )
    {
        $this->cacheManager = $cacheManager;
        $this->helper = $helper;
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            'preRemove',
            'preUpdate',
            'postUpdate'
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Post && !$entity instanceof User) {
            return;
        }
        $this->cacheManager->remove($this->helper->asset($entity, 'imageFile'));
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        dd($args);
        $entity = $args->getEntity();
        if (!$entity instanceof Post && !$entity instanceof User) {
            return;
        }

        if ($entity->getImageFile() instanceof UploadedFile) {
            /* dd($entity);
            $this->cacheManager->remove($this->helper->asset($entity, 'imageFile'));
            */
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        dd($args->getEntity());
    }
}

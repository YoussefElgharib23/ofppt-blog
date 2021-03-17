<?php

namespace App\Helper;

use App\Entity\Post;
use App\Entity\User;
use Enqueue\Client\ProducerInterface;
use Liip\ImagineBundle\Async\Commands;
use Liip\ImagineBundle\Async\ResolveCache;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CacheImage
{

    public static function LiipBackgroundCacheImage($entity, UploaderHelper $uploaderHelper, ProducerInterface $producer)
    {
        // if ($entity instanceof Post)
        // {
        //     $producer->sendCommand(Commands::RESOLVE_CACHE, new ResolveCache($uploaderHelper->asset($entity, 'imageFile'), array('thumb')));
        // }
        // else if ($entity instanceof User)
        // {
        //     $producer->sendCommand(Commands::RESOLVE_CACHE, new ResolveCache($uploaderHelper->asset($entity, 'imageFile'), array('profile_pic', 'profile_pic_min', 'profile_pic_min_table')));
        // }
    }
}
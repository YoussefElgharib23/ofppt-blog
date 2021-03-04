<?php

namespace App\EventSubscriber;

use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use Liip\ImagineBundle\Async\Commands;

class EnqueueCache implements Processor, TopicSubscriberInterface
{

    /**
     * @param Message $message
     * @param Context $context
     * @return object|string
     */
    public function process(Message $message, Context $context)
    {
        echo $message->getBody();
        return self::ACK;
    }

    /**
     * @return array|string
     */
    public static function getSubscribedTopics()
    {
        return [Commands::RESOLVE_CACHE];
    }
}
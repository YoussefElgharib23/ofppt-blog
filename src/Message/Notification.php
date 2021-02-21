<?php

namespace App\Message;

use App\Traits\TimeStampsTrait;

class Notification
{
    use TimeStampsTrait;

    /**
     * @var int
     */
    private $user_id;
    /**
     * @var int|null
     */
    private $post_id;
    /**
     * @var string
     */
    private $type;

    public function __construct(
        int $user_id,
        ?int $post_id,
        string $type
    )
    {
        $this->user_id = $user_id;
        $this->post_id = $post_id;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return int|null
     */
    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
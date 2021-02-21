<?php

namespace App\Message;

class SendNewEmail
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $content = null;
    /**
     * @var int
     */
    private $user_id;

    public function __construct(
        string $type,
        ?string $content,
        int $user_id
    )
    {
        $this->type = $type;
        $this->content = $content;
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }
}
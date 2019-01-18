<?php

namespace SubscriptionBundle\Entity\Game;


use App\Domain\Entity\Game;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use SubscriptionBundle\Entity\Subscription;

class SubscribedGame implements HasUuid
{
    /** @var string */
    private $uuid;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @var \DateTime
     */
    private $firstDownload;

    /**
     * @var \DateTime
     */
    private $lastDownload;

    /**
     * SubscribedGame constructor.
     *
     * @param $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param Game|int $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @param Subscription $subscription
     */
    public function setSubscription(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return \DateTime
     */
    public function getFirstDownload(): \DateTime
    {
        return $this->firstDownload;
    }

    /**
     * @param \DateTime $firstDownload
     */
    public function setFirstDownload(\DateTime $firstDownload)
    {
        $this->firstDownload = $firstDownload;
    }

    /**
     * @return \DateTime
     */
    public function getLastDownload(): \DateTime
    {
        return $this->lastDownload;
    }

    /**
     * @param \DateTime $lastDownload
     */
    public function setLastDownload(\DateTime $lastDownload)
    {
        $this->lastDownload = $lastDownload;
    }
}
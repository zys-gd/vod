<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 12:02
 */

namespace SubscriptionBundle\Service\Action\Renew\DTO;


class MassRenewResult
{
    /**
     * @var int
     */
    private $processed;
    /**
     * @var int
     */
    private $succeeded;
    /**
     * @var int
     */
    private $failed;
    /**
     * @var string|null
     */
    private $error;

    /**
     * MassRenewResult constructor.
     * @param int         $processed
     * @param int         $succeeded
     * @param int         $failed
     * @param string|null $error
     */
    public function __construct(int $processed, int $succeeded, int $failed, string $error = null)
    {
        $this->processed = $processed;
        $this->succeeded = $succeeded;
        $this->failed    = $failed;
        $this->error     = $error;
    }

    /**
     * @return int
     */
    public function getProcessed(): int
    {
        return $this->processed;
    }

    /**
     * @return int
     */
    public function getSucceeded(): int
    {
        return $this->succeeded;
    }

    /**
     * @return int
     */
    public function getFailed(): int
    {
        return $this->failed;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }


}
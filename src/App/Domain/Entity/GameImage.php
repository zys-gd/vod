<?php

namespace App\Domain\Entity;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class GameImage implements HasUuid
{
    /**
     * Constants used for determining the location of different resources for this entity
     */
    const RESOURCE_SCREENSHOTS = 'images/game_screenshots';

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $translations;

    /**
     * GameImage constructor.
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->translations = new ArrayCollection();
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
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
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? $this->getName() : 'GameImage';
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getNamePath()
    {
        return static::RESOURCE_SCREENSHOTS . '/' . $this->getName();
    }

    /**
     * Get file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return GameImage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set file
     *
     * @param File $file
     *
     * @return $this
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Validates the file field
     *
     * @param ExecutionContextInterface $context
     */
    public function validateFile(ExecutionContextInterface $context)
    {
        if (!$this->getName() && !$this->getFile()) {

            $context->buildViolation('Please select an image.')
                ->atPath('file')
                ->addViolation();
        }
    }
}

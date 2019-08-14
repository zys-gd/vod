<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.07.19
 * Time: 14:11
 */

namespace IdentificationBundle\WifiIdentification\DTO;


class PhoneValidationOptions
{
    /**
     * @var string
     */
    private $placeholder;
    /**
     * @var string
     */
    private $phoneRegexPattern;
    /**
     * @var string
     */
    private $pinRegexPattern;
    /**
     * @var string
     */
    private $pinPlaceholder;

    /**
     * PhoneValidationOptions constructor.
     * @param string $placeholder
     * @param string $phoneRegexPattern
     * @param string $pinPlaceholder
     * @param string $pinRegexPattern
     */
    public function __construct(string $placeholder, string $phoneRegexPattern, string $pinPlaceholder = '', string $pinRegexPattern = '')
    {
        $this->placeholder       = $placeholder;
        $this->phoneRegexPattern = $phoneRegexPattern;
        $this->pinRegexPattern   = $pinRegexPattern;
        $this->pinPlaceholder    = $pinPlaceholder;
    }

    /**
     * @return string
     */
    public function getPhonePlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function getPhoneRegexPattern(): string
    {
        return $this->phoneRegexPattern;
    }

    /**
     * @return string
     */
    public function getPinRegexPattern(): string
    {
        return $this->pinRegexPattern;
    }

    /**
     * @return string
     */
    public function getPinPlaceholder(): string
    {
        return $this->pinPlaceholder;
    }
}
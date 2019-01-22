<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 22.01.19
 * Time: 14:41
 */

namespace ExtrasBundle\SignatureCheck;


class ParametersProvider
{

    /**
     * @var string
     */
    private $signatureParameter;
    /**
     * @var string
     */
    private $signatureKey;

    /**
     * SignatureHandler constructor.
     * @param string $signatureParameter
     * @param string $signatureKey
     */
    public function __construct(string $signatureParameter, string $signatureKey)
    {
        $this->signatureParameter = $signatureParameter;
        $this->signatureKey       = $signatureKey;
    }

    /**
     * @return string
     */
    public function getSignatureParameter(): string
    {
        return $this->signatureParameter;
    }

    /**
     * @return string
     */
    public function getSignatureKey(): string
    {
        return $this->signatureKey;
    }




}
<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.01.19
 * Time: 18:13
 */

namespace ExtrasBundle\SignatureCheck;


use ExtrasBundle\SignatureCheck\Exception\InvalidSignatureException;

class SignatureHandler
{
    /**
     * @var ParametersProvider
     */
    private $config;

    /**
     * SignatureHandler constructor.
     */
    public function __construct(ParametersProvider $config)
    {
        $this->config = $config;
    }


    /**
     * @param string $signature
     * @param array  $requestBase
     * @throws \ExtrasBundle\SignatureCheck\Exception\InvalidSignatureException
     */
    public function performSignatureCheck(string $signature, array $requestBase): void
    {
        unset($requestBase[$this->config->getSignatureParameter()]);

        $validSign = $this->generateSign($requestBase);
        if ($signature !== $validSign) {
            throw new InvalidSignatureException('Signature is not valid. Probably request was tampered.', null, 400);
        }
    }

    public function generateSign(array $requestBase): string
    {
        unset($requestBase[$this->config->getSignatureParameter()]);

        ksort($requestBase);

        $signatureString = '';
        foreach ($requestBase as $val) {
            $signatureString .= $val;
        }

        $signatureString .= $this->config->getSignatureKey();

        return md5($signatureString);
    }
}
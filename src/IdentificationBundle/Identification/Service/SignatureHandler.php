<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.01.19
 * Time: 18:13
 */

namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Identification\Exception\InvalidSignatureException;

class SignatureHandler
{

    /**
     * @param string $signature
     * @param array  $requestBase
     * @throws InvalidSignatureException
     */
    public function performSignatureCheck(string $signature, array $requestBase): void
    {
        unset($requestBase['signature']);

        $validSign = $this->generateSign($requestBase);
        if ($signature !== $validSign) {
            throw new InvalidSignatureException('Signature is not valid. Probably request was tampered.', null, 400);
        }
    }

    public function generateSign(array $requestBase): string
    {
        unset($requestBase['signature']);

        ksort($requestBase);

        $signatureString = '';
        foreach ($requestBase as $val) {
            $signatureString .= $val;
        }

        $signatureString .= 'randomkey';

        return md5($signatureString);
    }
}
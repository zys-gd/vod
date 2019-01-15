<?php
/**
 * Date: 9/14/2016
 * Time: 17:39
 * @copyright (c) Zitec COM
 * @author Olga Luchinkina <luchinkina.olga@gmail.com>
 */

namespace PriceBundle\Entity;

interface FbTierValueInterface
{
    /**
     * Sets the Billing Framework process id
     * @param integer $bfProcessId
     */
    public function setBfProcessId($bfProcessId);

    /**
     * Returns the Billing Framework process id
     * @return integer
     */
    public function getBfProcessId();

    /**
     * Returns the Webshop process id
     * @return integer
     */
    public function getBfProductId();

    /**
     * Returns the external Billing Framework tier id
     * @return integer
     */
    public function getBfTierId();

    /**
     * Returns the external Billing Framework strategy id
     * @return integer
     */
    public function getBfStrategyId();

    /**
     * Sets the date expired
     * @param FbTierValueInterface $tierValue
     */
    public function bfPrepare(FbTierValueInterface $tierValue);
}
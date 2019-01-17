<?php
namespace PriceBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class TierValueRepository
 * @package PriceBundle\Repository
 */
class TierValueRepository extends EntityRepository
{

    /**
     * Returns a set of TierValues for a specific carriers
     * $carriers example: array(
     *   array(
     *       "carrier_id" => 1,
     *       "provider_id" => 2,
     *       "name" => "Vodafone"
     *   ),
     *   array(
     *       "carrier_id" => 2,
     *       "provider_id" => 1,
     *       "name" => "Orange"
     *   ),
     *       array(
     *       "carrier_id" => 3,
     *       "provider_id" => 2,
     *       "name" => "Telecom"
     *   ),
     * )
     *
     * @param array $countryCode
     */
    public function getByCarriers(array $carriers)
    {
        $db = $this->getEntityManager()->createQueryBuilder()
            ->select('tv')
            ->from('PriceBundle:TierValue', 'tv');

        foreach($carriers as $carrier){
            if ($carrier['carrier_id']) {
                $db->orWhere($db->expr()->andX(
                    $db->expr()->eq('tv.carrierId', $carrier['carrier_id']),
                    $db->expr()->eq('tv.billingAgregatorId', $carrier['provider_id'])
                ));
                
            } else {
                $db->orWhere($db->expr()->andX(
                    $db->expr()->eq('tv.billingAgregatorId', $carrier['provider_id'])
                ));
            }
        }
        return $db->getQuery()->getResult();
    }

}
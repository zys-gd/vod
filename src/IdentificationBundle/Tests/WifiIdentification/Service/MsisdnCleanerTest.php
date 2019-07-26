<?php declare(strict_types=1);


use IdentificationBundle\WifiIdentification\Service\MsisdnCleaner;
use PHPUnit\Framework\TestCase;

class MsisdnCleanerTest extends TestCase
{
    /** @var MsisdnCleaner */
    private $msisdnCleaner;

    protected function setUp()
    {
        $this->msisdnCleaner = new MsisdnCleaner();
    }

    public function testCleanTelenorPK()
    {

        $carrier = Mockery::mock(\CommonDataBundle\Entity\Interfaces\CarrierInterface::class);

        $carrier->allows(['getBillingCarrierId' => 381]);

        $result = $this->msisdnCleaner->clean('+380915151', $carrier);

        $this->assertEquals('380915151', $result);
    }

    public function testCleanCellcardCambodia()
    {

        $carrier = Mockery::mock(\CommonDataBundle\Entity\Interfaces\CarrierInterface::class);

        $carrier->allows(['getBillingCarrierId' => 2207]);

        $result = $this->msisdnCleaner->clean('+380915151', $carrier);

        $this->assertEquals('380915151', $result);
    }

}

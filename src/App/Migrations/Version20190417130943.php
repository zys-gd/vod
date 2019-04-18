<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Domain\Entity\Carrier;
use Doctrine\ORM\AbstractQuery;
use Doctrine\DBAL\Schema\Schema;
use \Doctrine\DBAL\DBALException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Migrations\AbstractMigration;
use SubscriptionBundle\Entity\SubscriptionPack;
use \Doctrine\DBAL\Migrations\AbortMigrationException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190417130943 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var EntityManagerInterface $entityManager*/
    private $entityManager;

    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }

    /**
     * @param Schema $schema
     *
     */
    public function postUp(Schema $schema)
    {
        $this->entityManager =  $this->container->get('doctrine.orm.entity_manager');

        /** @var array $allPacksWithCarrierUuids */
        $allPacksWithCarrierUuids = $this->getAllSubscriptionPacksWithCarrierUuids();

        $index = 0;

        /** @var array(SubscriptionPack, carrierUuid) $packWithCarrierUuid */
        foreach ($allPacksWithCarrierUuids as $key => $subscriptionPack) {
            if (gettype($key) === 'integer') {
                continue;
            }

            /** @var SubscriptionPack $pack */
            $pack = $subscriptionPack;
            /** @var string $carrierUuid */
            $carrier = $allPacksWithCarrierUuids[$index++];

            $pack->setCarrier($carrier);

            $this->entityManager->persist($pack);
        }

        $this->entityManager->flush();
    }

    /**
     * @return array
     */
    private function getAllSubscriptionPacksWithCarrierUuids()
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('sp', 'c')
            ->from(SubscriptionPack::class, 'sp','sp.uuid')
            ->join(Carrier::class,'c', Join::WITH, 'sp.carrierId = c.billingCarrierId')
            ->getQuery();

        $spCarriers = $query->getResult(AbstractQuery::HYDRATE_OBJECT);

        return $spCarriers;
    }
}

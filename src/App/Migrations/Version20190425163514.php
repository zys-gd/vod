<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Language;
use App\Utils\UuidGenerator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190425163514 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    public function postUp(Schema $schema)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $languageRepository = $em->getRepository(Language::class);

        /** @var Language $arabicLanguage */
        $arabicLanguage = $languageRepository->findOneBy(['code' => 'ar']);

        $vodafoneEGCarrier = new Carrier('99a362ea-72cd-45d5-bbcc-18f16b8451ed');
        $vodafoneEGCarrier
            ->setDefaultLanguage($arabicLanguage)
            ->setBillingCarrierId(2253) // todo change for live
            ->setName('Vodafone EG via TPAY')
            ->setCountryCode('EG')
            ->setIsp('Vodafone Egypt')
            ->setPublished(true)
            ->setLpOtp(true)
            ->setPinIdentSupport(true)
            ->setTrialInitializer('carrier')
            ->setTrialPeriod(0)
            ->setSubscriptionPeriod(1)
            ->setResubAllowed(false)
            ->setOperatorId(0)
            ->setNumberOfAllowedSubscription(5) // todo
            ->setIsCampaignsOnPause(false);

        $orangeEGCarrier = new Carrier('7c8385df-8d56-464a-98ff-66c55a7a5741');
        $orangeEGCarrier
            ->setDefaultLanguage($arabicLanguage)
            ->setBillingCarrierId(2254) // todo change for live
            ->setName('Orange EG via TPAY')
            ->setCountryCode('EG')
            ->setIsp('Orange Egypt')
            ->setPublished(true)
            ->setLpOtp(true)
            ->setPinIdentSupport(true)
            ->setTrialInitializer('carrier')
            ->setTrialPeriod(0)
            ->setSubscriptionPeriod(1)
            ->setResubAllowed(false)
            ->setOperatorId(0)
            ->setNumberOfAllowedSubscription(5) //todo
            ->setIsCampaignsOnPause(false);

        $em->persist($vodafoneEGCarrier);
        $em->persist($orangeEGCarrier);

        $em->flush();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Entity\VideoPartner;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190408162057 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }

    public function postUp(Schema $schema)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $videoPartnerRepository = $em->getRepository(VideoPartner::class);
        $uploadedVideoRepository = $em->getRepository(UploadedVideo::class);

        /** @var VideoPartner $redbullVideoPartner */
        $redbullVideoPartner = $videoPartnerRepository->find('7ed37c75-da2d-4842-89ff-e0af8f8ddfea');
        /** @var VideoPartner $sntvVideoPartner */
        $sntvVideoPartner = $videoPartnerRepository->find('afed2496-38f0-4a02-ab25-a6bc18aeefed');

        $extremeSportSubcategoryUuid = '541d47f69-49a4-4823-a7f9-574ac1fl4hd';

        /** @var UploadedVideo $uploadedVideo */
        foreach ($uploadedVideoRepository->findAll() as $uploadedVideo) {
            if ($uploadedVideo->getSubcategory()->getUuid() === $extremeSportSubcategoryUuid) {
                if (!empty($redbullVideoPartner)) {
                    $uploadedVideo->setVideoPartner($redbullVideoPartner);
                }
            } else {
                if (!empty($sntvVideoPartner)) {
                    $uploadedVideo->setVideoPartner($sntvVideoPartner);
                }
            }

            $em->persist($uploadedVideo);
        }

        $em->flush();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}

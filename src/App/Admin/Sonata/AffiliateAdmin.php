<?php

namespace App\Admin\Sonata;

use App\Admin\Form\Type\AffiliateBannedPublisherType;
use App\Admin\Form\Type\AffiliateConstantType;
use App\Admin\Form\Type\AffiliateParameterType;
use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Repository\AffiliateRepository;
use App\Domain\Service\Campaign\CampaignService;
use CommonDataBundle\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class AffiliateAdmin
 */
class AffiliateAdmin extends AbstractAdmin
{
    /**
     * @var AffiliateRepository
     */
    private $affiliateRepository;
    /**
     * @var CampaignService
     */
    private $campaignService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AffiliateAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param AffiliateRepository $affiliateRepository
     * @param CampaignService $campaignService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        AffiliateRepository $affiliateRepository,
        CampaignService $campaignService,
        EntityManagerInterface $entityManager
    ) {
        $this->affiliateRepository = $affiliateRepository;
        $this->campaignService     = $campaignService;
        $this->entityManager = $entityManager;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return Affiliate
     * @throws \Exception
     */
    public function getNewInstance(): Affiliate
    {
        return new Affiliate(UuidGenerator::generate());
    }

    /**
     * @param Affiliate $obj
     */
    public function preUpdate($obj)
    {
        $this->generateTestLink($obj);

        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($obj);
        if(count($originalData) > 0) {
            if ($obj->isOneClickFlow() != $originalData['isOneClickFlow']) {
                $isOneClickFlow = $obj->isOneClickFlow();
                $obj->getCampaigns()->map(function (Campaign $campaign) use ($isOneClickFlow) {
                    $campaign->setIsOneClickFlow($isOneClickFlow);
                });
            }
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('uuid')
            ->add('type')
            ->add('url')
            ->add('country')
            ->add('commercialContact')
            ->add('technicalContact')
            ->add('skypeId')
            ->add('isOneClickFlow')
            ->add('enabled');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('uuid')
            ->add('url')
            ->add('isOneClickFlow')
            ->add('enabled', null, [
                'label' => 'Is Enabled?'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('type')
            ->add('url')
            ->add('country')
            ->add('commercialContact')
            ->add('technicalContact')
            ->add('skypeId')
            ->add('isOneClickFlow', null, [
                'label' => 'Turn off LP showing',
                'help'  => 'If consent page exist, then show it. Otherwise will try to subscribe'
            ])
            ->add('enabled');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->buildGeneralSection($formMapper);
        $this->buildContactSection($formMapper);
        $this->buildMiscSection($formMapper);
        $this->buildConstantSection($formMapper);
        $this->buildParametersSection($formMapper);
        $this->buildUniqueFlowSection($formMapper);
        $this->buildBannedPublishersSection($formMapper);

    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildGeneralSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
            ->with('', ['box_class' => 'box-solid'])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'CPC' => Affiliate::CPC_TYPE,
                    'CPA' => Affiliate::CPA_TYPE
                ],
                'label'   => 'Type'
            ])
            ->add('name', TextType::class, [
                'label'       => 'Name',
                'constraints' => [
                    new Callback(function (string $name, ExecutionContextInterface $context) {
                        $affiliates = $this->affiliateRepository->findBy(['name' => $name]);
                        /** @var Affiliate $affiliate */
                        $affiliate = empty($affiliates) ? null : $affiliates[0];
                        $subject   = $this->getSubject();

                        if ($affiliate && $affiliate->getUuid() !== $subject->getUuid()) {
                            $context
                                ->buildViolation('Affiliate with the same name already exists')
                                ->addViolation();
                        }
                    })
                ]
            ])
            ->add('postbackUrl', UrlType::class, [
                'required'    => true,
                'label'       => 'Postback URL',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('isOneClickFlow', ChoiceFieldMaskType::class, [
                'label'   => 'Turn off LP showing',
                'help'    => 'If consent page exist, then show it. Otherwise will try to subscribe',
                'choices' => [
                    'No'  => 0,
                    'Yes' => 1,
                ],
                'map'     => [
                    1 => ['carriers', 'carriers2'],
                ],
            ])
            ->add('carriers', EntityType::class, [
                'class'       => Carrier::class,
                'expanded'    => false,
                'required'    => false,
                'multiple'    => true,
                'placeholder' => 'Please select carriers',
                'help'        => 'If empty, then for all carriers. Otherwise landing will be turned off only for chosen carriers.'
            ])
            ->end()
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildContactSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Contact details')
            ->with('', ['box_class' => 'box-solid'])
            ->add('country', EntityType::class, [
                'class'        => Country::class,
                'choice_label' => 'countryName',
                'label'        => 'Based in'
            ])
            ->add('commercialContact', TextType::class, [
                'required' => false,
                'label'    => 'Commercial Contact person'
            ])
            ->add('technicalContact', TextType::class, [
                'required' => false,
                'label'    => 'Technical Contact person'
            ])
            ->add('skypeId', TextType::class, [
                'required' => false,
                'label'    => 'Skype ID'
            ])
            ->add('url', TextType::class, [
                'required' => false
            ])
            ->end()
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildMiscSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Other options')
            ->with('', ['box_class' => 'box-solid'])
            ->add('enabled', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No'  => false
                ],
                'label'   => 'Enable this affiliate?'
            ])
            ->add('subPriceName', TextType::class, [
                'required' => false,
                'label'    => 'Name of subscription price parameter. Fill JUST when the partner requires!'
            ])
            ->end()
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildConstantSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Constants')
            ->with('', ['box_class' => 'box-solid'])
            ->add('constants', CollectionType::class, [
                'entry_type'   => AffiliateConstantType::class,
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add'    => true
            ])
            ->end()
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildParametersSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Parameters')
            ->with('', ['box_class' => 'box-solid'])
            ->add('parameters', CollectionType::class, [
                'entry_type'   => AffiliateParameterType::class,
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add'    => true
            ])
            ->end()
            ->end();
    }

    private function buildUniqueFlowSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Unique Flow')
            ->with('Affiliate', ['box_class' => 'box box-primary'])
            ->add('uniqueFlow', CheckboxType::class, [
                'required' => false,
                'label'    => 'Unique Flow'
            ])
            ->add('uniqueParameter', TextType::class, [
                'required' => false,
                'label'    => 'Unique Parameter'
            ])
            ->end()
            ->end();
    }

    private function buildBannedPublishersSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Banned Publishers')
            ->with('', ['box_class' => 'box-solid'])
            ->add('bannedPublishers', CollectionType::class, [
                'entry_type'   => AffiliateBannedPublisherType::class,
                'allow_delete' => true,
                'allow_add'    => true,
                'by_reference' => false,
                'label'        => 'Publisher identification keys'
            ])
            ->end();
    }

    public function postUpdate($object)
    {
        $this->affiliateRepository->switchStatusRelatedCampaigns($object);
    }

    /**
     * @param Affiliate $affiliate
     */
    protected function generateTestLink(Affiliate $affiliate)
    {
        /** @var CampaignInterface[] $campaigs */
        $campaigns = $affiliate->getCampaigns();

        foreach ($campaigns as $campaign) {
            $this->campaignService->generateTestLink($campaign);
        }
    }
}

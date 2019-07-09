<?php

namespace SubscriptionBundle\Admin\Sonata;

use App\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Entity\BlackList;
use SubscriptionBundle\Service\Blacklist\BlacklistChecker;
use SubscriptionBundle\Service\Blacklist\BlacklistSaver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class BlackListAdmin
 */
class BlackListAdmin extends AbstractAdmin
{
    /**
     * @var BlacklistChecker
     */
    private $blackListService;

    /**
     * BlackListAdmin constructor
     *
     * @param string           $code
     * @param string           $class
     * @param string           $baseControllerName
     * @param BlacklistChecker $blackListService
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        BlacklistSaver $blackListService
    )
    {
        $this->blackListService = $blackListService;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param BlackList $blackList
     */
    public function postPersist($blackList)
    {
        $this->blackListService->doAfterAddedToBlackList($blackList);
    }

    /**
     * @param BlackList $blacklist
     */
    public function postUpdate($blacklist)
    {
        $this->postPersist($blacklist);
    }

    /**
     * @return BlackList
     * @throws \Exception
     */
    public function getNewInstance(): BlackList
    {
        return new BlackList(UuidGenerator::generate());
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('alias');
    }



    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('alias')
            ->add('duration', 'choice', [
                'choices' => array_flip(array_change_key_case(BlackList::PERIODICITY_TYPE, CASE_UPPER)),
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
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('billingCarrierId')
            ->add('alias', TextType::class, [
                'required' => true
            ])
            ->add('duration', ChoiceFieldMaskType::class, [
                'choices'  => array_change_key_case(BlackList::PERIODICITY_TYPE, CASE_UPPER),
                'required' => true,
                'map'      => [
                    1 => ['ban_start', 'ban_end'],
                ],
            ])
            ->add('ban_start', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'input'  => 'datetime',
                'attr'   => ['style' => 'width: 12vw'],
                'required' => false
            ])
            ->add('ban_end', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'input'  => 'datetime',
                'attr'   => ['style' => 'width: 12vw'],
            ]);
        // ->add('ban_end', DatePickerType::class, ['input' => 'datetime',]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {

        $showMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('alias')
            ->add('duration', 'choice', [
                'choices' => array_flip(array_change_key_case(BlackList::PERIODICITY_TYPE, CASE_UPPER)),
                'map'     => [
                    1 => ['ban_start', 'ban_end'],
                ],
            ])
            ->add('ban_start')
            ->add('ban_end');
    }
}
<?php

namespace App\Admin\Sonata;

use App\Admin\Form\Type\AffiliateConstantType;
use App\Admin\Form\Type\AffiliateParameterType;
use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Country;
use App\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AffiliateAdmin
 */
class AffiliateAdmin extends AbstractAdmin
{
    /**
     * @return Affiliate
     *
     * @throws \Exception
     */
    public function getNewInstance()
    {
        return new Affiliate(UuidGenerator::generate());
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
            ->add('enabled', null, [
                'label' => 'Is Enabled?'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
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
                'label' => 'Type'
            ])
            ->add('name', TextType::class, [
                'label' => 'Name'
            ])
            ->add('postbackUrl', UrlType::class, [
                'required' => true,
                'label' => 'Postback URL',
                'constraints' => [
                    new NotBlank()
                ]
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
                'class' => Country::class,
                'choice_label' => 'countryName',
                'label' => 'Based in'
            ])
            ->add('commercialContact', TextType::class, [
                'required' => false,
                'label' => 'Commercial Contact person'
            ])
            ->add('technicalContact', TextType::class, [
                'required' => false,
                'label' => 'Technical Contact person'
            ])
            ->add('skypeId', TextType::class, [
                'required' => false,
                'label' => 'Skype ID'
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
                    'No' => false
                ],
                'label' => 'Enable this affiliate?'
            ])
            ->add('subPriceName', TextType::class, [
                'required' => false,
                'label' => 'Name of subscription price parameter. Fill JUST when the partner requires!'
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
                'entry_type' => AffiliateConstantType::class,
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add' => true
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
                'entry_type' => AffiliateParameterType::class,
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add' => true
            ])
            ->end()
            ->end();
    }
}

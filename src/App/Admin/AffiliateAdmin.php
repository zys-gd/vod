<?php

namespace App\Admin;

use App\Domain\Entity\Affiliate;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Validator\ErrorElement;


class AffiliateAdmin extends AbstractAdmin
{


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
            ->add('type', 'choice', [
                'choices' => [
                    'CPC' => Affiliate::CPC_TYPE,
                    'CPA' => Affiliate::CPA_TYPE
                ],
                'label' => 'Type'
            ])
            ->add('name', 'text', ['label' => 'Name'])
//            ->add('url', 'url', ['label' => 'URL'])
            ->add('postbackUrl', 'url',
                [
                'required' => true,
                'label' => 'Postback URL',
                ]
            )
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
            ->add('country', 'entity', [
                'class' => 'App\Domain\Entity\Country',
                'choice_label' => 'countryName',
                'label' => 'Based in'
            ])
            ->add('commercialContact', 'text', ['required' => false, 'label' => 'Commercial Contact person'])
            ->add('technicalContact', 'text', ['required' => false, 'label' => 'Technical Contact person'])
            ->add('skypeId', 'text', ['required' => false, 'label' => 'Skype ID'])
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
            ->add('enabled', 'choice', [
                'choices' => [
                    'Yes' => true,
                    'No' => false
                ],
                'label' => 'Enable this affiliate?'
            ])
            ->add('subPriceName', 'text', ['required' => false, 'label' => 'Name of subscription price parameter. Fill JUST when the partner requires!'])
            ->end()
            ->end();
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('id')
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
            ->add('id')
            ->add('url')
            ->add('enabled', null, ['label' => 'Is Enabled?'])
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
    private function buildConstantSection(FormMapper $formMapper)
    {

        $formMapper
            ->tab('Constants')
            ->with('', ['box_class' => 'box-solid'])
//            ->add('constants', 'sonata_type_collection', [
            ->add('constants', CollectionType::class, [
                'by_reference' => true,
                'type_options' => [
                    'delete' => true,
                ],
                'required' => true,
            ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
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
            ->add('parameters', 'sonata_type_collection', [
                'by_reference' => true,
                'type_options' => [
                    'delete' => true,

                ],
                'required' => true,
            ],
                [
                    'edit' => 'inline',
                    'inline' => 'table'
                ])
            ->end()
            ->end();
    }



    public function validate(ErrorElement $errorElement, $object)
    {

        $errorElement
            ->with("postbackUrl")
            ->assertNotBlank()
            ->end();

        foreach ($object->getConstants() as $constantsProps) {

            if ($constantsProps->getName() == null) {
                $error = 'Constant field "Name" should not be blank';
                $errorElement->with('constant')->addViolation($error)->end();
                break;
            }


            if ($constantsProps->getValue() == null) {
                $error = 'Constant field "Value" should not be blank';
                $errorElement->with('constants')->addViolation($error)->end();
                break;
            }
        }

        foreach ($object->getParameters() as $paramsProps) {

            if ($paramsProps->getInputName() == null) {
                $error = 'Parameters field "Input Name" should not be blank';
                $errorElement->with('parameters')->addViolation($error)->end();
                break;
            }


            if ($paramsProps->getOutputName() == null) {
                $error = 'Parameters field "Output Name" should not be blank';
                $errorElement->with('constants')->addViolation($error)->end();
                break;
            }
        }


    }






}

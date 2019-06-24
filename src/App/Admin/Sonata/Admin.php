<?php

namespace App\Admin\Sonata;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class AdminAdmin
 */
class Admin extends AbstractAdmin
{

    const IGNORE_ROLES = [
        'ROLE_SUPER_ADMIN'
    ];

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * Admin constructor.
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param UserManagerInterface $userManager
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        UserManagerInterface $userManager
    ) {
        $this->userManager = $userManager;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param \App\Domain\Entity\Admin $object
     */
    public function preUpdate($object)
    {
        $this->userManager->updateCanonicalFields($object);
        $this->userManager->updatePassword($object);
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        return [
            "id",
            "username",
            "usernameCanonical",
            "email",
            "emailCanonical",
            "enabled",
            "lastLogin",
            "confirmationToken",
            "passwordRequestedAt"
        ];
    }

    /**
     * @param DatagridMapper $filterMapper
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username')
            ->add('email');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username')
            ->add('email')
            ->add('roles')
            ->add('enabled', null, [
                'editable' => true
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);;
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'required' => true
            ])
            ->add('plainPassword', TextType::class, [
                'required' => $this->isCurrentRoute('create')
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => $this->getRoles(),
                'multiple' => true,
                'constraints' => [
                    new Callback([$this, 'validateRoles'])
                ]
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false
            ]);
    }

    /**
     * @return array
     */
    private function getRoles(): array
    {
        $container = $this->getConfigurationPool()->getContainer();
        $roles = $container->getParameter('security.role_hierarchy.roles');
        $preparedRoles = [];

        foreach ($roles as $role => $hierarchy) {
            if (!in_array($role, self::IGNORE_ROLES)) {
                $preparedRoles[$role] = $role;
            }
        }

        return $preparedRoles;
    }

    public function validateRoles(?array $roles, ExecutionContextInterface $context):void
    {
        $availableRoles = $this->getRoles();

        foreach ($roles as $role) {
            if(!in_array($role, $availableRoles) || in_array($role, self::IGNORE_ROLES)) {
                $context
                    ->buildViolation('Permission denied')
                    ->addViolation();
            }
        }
    }
}
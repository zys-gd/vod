<?php

namespace App\Admin\Controller;

use App\Domain\Entity\Admin;
use DataDog\AuditBundle\Entity\AuditLog;
use DataDog\PagerBundle\Pagination;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AuditController extends AbstractController
{
    use DoctrineControllerTrait;

    public function filters(QueryBuilder $qb, $key, $val)
    {
        switch ($key) {
        case 'history':
            if ($val) {
                $orx = $qb->expr()->orX();
                $orx->add('s.fk = :fk');
                $orx->add('t.fk = :fk');

                $qb->andWhere($orx);
                $qb->setParameter('fk', intval($val));
            }
            break;
        case 'class':
            $orx = $qb->expr()->orX();
            $orx->add('s.class = :class');
            $orx->add('t.class = :class');

            $qb->andWhere($orx);
            $qb->setParameter('class', $val);
            break;
        case 'blamed':
            if ($val === 'null') {
                $qb->andWhere($qb->expr()->isNull('a.blame'));
            } else {
                // this allows us to safely ignore empty values
                // otherwise if $qb is not changed, it would add where the string is empty statement.
                $qb->andWhere($qb->expr()->eq('b.fk', ':blame'));
                $qb->setParameter('blame', $val);
            }
            break;
        default:
            // if user attemps to filter by other fields, we restrict it
            throw new \Exception("filter not allowed");
        }
    }

    /**
     * @Route("/", name="audit")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        $blocks = array(
            'top' => array(),
            'left' => array(),
            'center' => array(),
            'right' => array(),
            'bottom' => array(),
        );
        foreach ($this->container->getParameter('sonata.admin.configuration.dashboard_blocks') as $block) {
            $blocks[$block['position']][] = $block;
        }

        Pagination::$defaults = array_merge(Pagination::$defaults, ['limit' => 25]);
        $qb = $this->repo("DataDogAuditBundle:AuditLog")
            ->createQueryBuilder('a')
            ->addSelect('s', 't', 'b')
            ->innerJoin('a.source', 's')
            ->leftJoin('a.target', 't')
            ->leftJoin('a.blame', 'b');
        $options = [
            'sorters' => ['a.loggedAt' => 'DESC'],
            'applyFilter' => [$this, 'filters'],
        ];
        $sourceClasses = [
            Pagination::$filterAny => 'Any Source Class',
        ];
        foreach ($this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata() as $meta) {
            if ($meta->isMappedSuperclass || strpos($meta->name, 'DataDog\AuditBundle') === 0) {
                continue;
            }
            $parts = explode('\\', $meta->name);
            $sourceClasses[$meta->name] = end($parts);
        }
        $users = [
            Pagination::$filterAny => 'Any User',
        ];
        /** @var Admin $user */
        foreach ($this->repo('App\Domain\Entity\Admin')->findAll() as $user) {
            $users[$user->getId()] = (string) $user;
        }
        $logs = new Pagination($qb, $request, $options);
        $parameters = array(
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $blocks,
            'logs' => $logs,
            'sourceClasses' => $sourceClasses,
            'users' => $users
        );

        return $this->render('@Admin/Audit/index.html.twig', $parameters);
    }

    /**
     * @Route("/diff/{id}", name="audit_diff")
     * @param AuditLog $log
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function diffAction(AuditLog $log)
    {
        $parameters = [
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'log' => $log,
            'diff' => json_decode($log->getDiff(), true)
        ];

        return $this->render('@Admin/Audit/diff.html.twig', $parameters);
    }


    /**
     * @param Request $request
     *
     * @return string
     */
    protected function getBaseTemplate()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->getAdminPool()->getTemplate('ajax');
        }

        return $this->getAdminPool()->getTemplate('layout');
    }

    /**
     * Get the request object from the container.
     *
     * This method is compatible with both Symfony 2.3 and Symfony 3
     *
     * NEXT_MAJOR: remove this method.
     *
     * @deprecated Use the Request action argument. This method will be removed
     *             in SonataAdminBundle 4.0 and the action methods adjusted
     *
     * @return Request
     */
    public function getRequest()
    {
        if ($this->container->has('request_stack')) {
            return $this->container->get('request_stack')->getCurrentRequest();
        }

        return $this->container->get('request');
    }

    /**
     * @return Pool
     */
    protected function getAdminPool()
    {
        return $this->container->get('sonata.admin.pool');
    }
}

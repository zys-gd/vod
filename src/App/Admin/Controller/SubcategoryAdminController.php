<?php

namespace App\Admin\Controller;

use App\Domain\Entity\Subcategory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SubcategoryAdminController
{
    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * SubcategoryAdminController constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     */
    public function subcategoriesListAction(Request $request)
    {
        $mainCategoryId = $request->query->get('mainId');

        if (empty($mainCategoryId)) {
            throw new BadRequestHttpException('Invalid parameter "mainId"');
        }

        $subcategories = $this->entityManager->getRepository(Subcategory::class)->findBy(['parent' => $mainCategoryId]);

        return new Response(json_encode($subcategories));
    }
}
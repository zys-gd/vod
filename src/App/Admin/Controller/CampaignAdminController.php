<?php

namespace App\Admin\Controller;

use App\Admin\Form\CampaignCloneForm;
use App\Domain\Entity\Campaign;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CampaignAdminController
 */
class CampaignAdminController extends CRUDController
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * CampaignAdminController constructor
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function cloneAction($id, Request $request)
    {
        /** @var Campaign $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $count = $request->get('count', 1);

        try {
            while ($count--) {
                $clonedObject = clone $object;
                $clonedObject->setCampaignToken(uniqid());

                $this->admin->create($clonedObject);
            }
        } catch (\Throwable $e) {
        }

        $this->addFlash('sonata_flash_success', 'Cloned successfully: ' . $request->get('count', 1));

        return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()]));
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function cloneConfirmAction($id, Request $request)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }
        $form = $this->formFactory->create(CampaignCloneForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            return new RedirectResponse($this->admin->generateUrl('clone', ['count' => $formData['count'], 'id' => $id, 'filter' => $this->admin->getFilterParameters()]));
        }

        return $this->renderWithExtraParams('@Admin/Campaign/clone_confirm.html.twig', [
            'form'   => $form->createView(),
            'object' => $object
        ]);
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request|null $request
     *
     * @return RedirectResponse
     */
    public function batchActionPause(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setIsPause(true);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'Cant update');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'Success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     *
     * @return RedirectResponse
     */
    public function batchActionUnpause(ProxyQueryInterface $selectedModelQuery)
    {
        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();

        try {
            /** @var Campaign $selectedModel */
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setIsPause(false);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $exception) {
            $this->addFlash('sonata_flash_error', 'Cant update');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'Success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     *
     * @return RedirectResponse
     */
    public function batchActionEnableOneClick(ProxyQueryInterface $selectedModelQuery)
    {
        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();

        try {
            /** @var Campaign $selectedModel */
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setIsLpOff(true);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $exception) {
            $this->addFlash('sonata_flash_error', 'Cant update');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'Success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     *
     * @return RedirectResponse
     */
    public function batchActionDisableOneClick(ProxyQueryInterface $selectedModelQuery)
    {
        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();

        try {
            /** @var Campaign $selectedModel */
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setIsLpOff(false);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $exception) {
            $this->addFlash('sonata_flash_error', 'Cant update');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'Success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     *
     * @return RedirectResponse
     */
    public function batchActionEnableClickableImage(ProxyQueryInterface $selectedModelQuery)
    {
        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();

        try {
            /** @var Campaign $selectedModel */
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setIsClickableSubImage(true);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $exception) {
            $this->addFlash('sonata_flash_error', 'Cant update');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'Success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     *
     * @return RedirectResponse
     */
    public function batchActionDisableClickableImage(ProxyQueryInterface $selectedModelQuery)
    {
        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();

        try {
            /** @var Campaign $selectedModel */
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setIsClickableSubImage(false);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $exception) {
            $this->addFlash('sonata_flash_error', 'Cant update');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'Success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }
}
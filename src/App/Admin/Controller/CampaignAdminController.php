<?php


namespace App\Admin\Controller;


use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CampaignAdminController extends CRUDController
{
    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function cloneAction($id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $clonedObject = clone $object;

        $this->admin->create($clonedObject);

        $this->addFlash('sonata_flash_success', 'Cloned successfully');

        return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()]));
    }

    public function batchActionPause(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();

        // do the merge work here

        try {
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setIsPause(true);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_merge_error');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'flash_batch_merge_success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }
}
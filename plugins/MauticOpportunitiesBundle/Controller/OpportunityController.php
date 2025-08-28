<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\GenericEvent;

class OpportunityController extends AbstractStandardFormController
{
    protected function getTemplateBase(): string
    {
        return '@MauticOpportunities/Opportunity';
    }

    protected function getModelName(): string
    {
        return 'opportunity';
    }

    /**
     * @param int $page
     */
    public function indexAction(Request $request, $page = 1): Response
    {
        return parent::indexStandard($request, $page);
    }

    /**
     * Generates new form and processes post data.
     *
     * @return JsonResponse|Response
     */
    public function newAction(Request $request)
    {
        return parent::newStandard($request);
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return JsonResponse|Response
     */
    public function editAction(Request $request, $objectId, $ignorePost = false)
    {
        return parent::editStandard($request, $objectId, $ignorePost);
    }

    /**
     * Displays opportunity details.
     *
     * @return array|JsonResponse|RedirectResponse|Response
     */
    public function viewAction(Request $request, $objectId)
    {
        return parent::viewStandard($request, $objectId, 'opportunity', 'plugin.opportunities', null, 'opportunity');
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAction(Request $request, $objectId)
    {
        return parent::deleteStandard($request, $objectId);
    }

    /**
     * Deletes a group of entities.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function batchDeleteAction(Request $request)
    {
        return parent::batchDeleteStandard($request);
    }

    public function contactsAction(Request $request, $objectId): Response
    {
        $model = $this->getModel('opportunity');
        /** @var \MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity|null $opportunity */
        $opportunity = $model->getEntity($objectId);
        if (null === $opportunity) {
            return $this->notFound('opportunity', $objectId);
        }

        $page  = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 20);
        $data  = $model->getAttachedContacts($opportunity, $page, $limit);

        return new JsonResponse($data);
    }

    public function searchContactsAction(Request $request, $objectId): Response
    {
        $model = $this->getModel('opportunity');
        /** @var \MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity|null $opportunity */
        $opportunity = $model->getEntity($objectId);
        if (null === $opportunity) {
            return $this->notFound('opportunity', $objectId);
        }

        $term  = (string) $request->query->get('q', '');
        if ('undefined' === $term || 'null' === $term) {
            $term = '';
        }
        $page       = $request->query->getInt('page', 1);
        $limit      = $request->query->getInt('limit', 10);
        // Since opportunities now have a direct contact relationship, exclude the currently attached contact
        $excludeIds = $opportunity->getContact() ? [$opportunity->getContact()->getId()] : [];
        $selected   = $request->query->get('exclude');
        if (!is_array($selected)) {
            $selected = $selected !== null ? [$selected] : [];
        }
        $excludeIds = array_merge($excludeIds, array_map('intval', $selected));
        $results    = $model->searchContacts($term, $excludeIds, $page, $limit);

        return new JsonResponse(['results' => $results]);
    }

    public function attachContactsAction(Request $request, $objectId): Response
    {
        if (!$this->security->isGranted('opportunities:opportunities:edit')) {
            return $this->accessDenied();
        }

        $model = $this->getModel('opportunity');
        /** @var \MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity|null $opportunity */
        $opportunity = $model->getEntity($objectId);
        if (null === $opportunity) {
            return $this->notFound('opportunity', $objectId);
        }

        $data = json_decode($request->getContent(), true);
        $ids  = $data['contactIds'] ?? null;
        if (!is_array($ids)) {
            return $this->badRequest('Invalid contactIds');
        }

        // Since opportunities now have a direct contact relationship, only attach the first contact
        if (!empty($ids)) {
            $model->attachContact($opportunity, (int) $ids[0]);
        }

        foreach ($ids as $id) {
            $this->dispatcher->dispatch(new GenericEvent(null, [
                'opportunityId' => $opportunity->getId(),
                'contactId'     => (int) $id,
                'actorId'       => $this->user->getId(),
                'action'        => 'attach',
            ]), 'plugin.opportunities.contact');
        }

        return new JsonResponse(['success' => true]);
    }

    public function detachContactAction(Request $request, $objectId, $contactId): Response
    {
        if (!$this->security->isGranted('opportunities:opportunities:edit')) {
            return $this->accessDenied();
        }

        $model = $this->getModel('opportunity');
        /** @var \MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity|null $opportunity */
        $opportunity = $model->getEntity($objectId);
        if (null === $opportunity) {
            return $this->notFound('opportunity', $objectId);
        }

        $model->detachContact($opportunity);

        $this->dispatcher->dispatch(new GenericEvent(null, [
            'opportunityId' => $opportunity->getId(),
            'contactId'     => (int) $contactId,
            'actorId'       => $this->user->getId(),
            'action'        => 'detach',
        ]), 'plugin.opportunities.contact');

        return new JsonResponse(['success' => true]);
    }
}
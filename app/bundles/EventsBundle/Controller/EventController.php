<?php

namespace MauticPlugin\MauticEventsBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractStandardFormController;
use MauticPlugin\MauticEventsBundle\Entity\EventContact;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\GenericEvent;

class EventController extends AbstractStandardFormController
{
    protected function getTemplateBase(): string
    {
        return '@MauticEvents/Event';
    }

    protected function getModelName(): string
    {
        return 'event';
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
     * Displays event details.
     *
     * @return array|JsonResponse|RedirectResponse|Response
     */
    public function viewAction(Request $request, $objectId)
    {
        return parent::viewStandard($request, $objectId, 'event', 'plugin.events', null, 'event');
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
        $model = $this->getModel('event');
        /** @var \MauticPlugin\MauticEventsBundle\Entity\Event|null $event */
        $event = $model->getEntity($objectId);
        if (null === $event) {
            return $this->notFound('event', $objectId);
        }

        $page  = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 20);
        $data  = $model->getAttachedContacts($event, $page, $limit);

        return new JsonResponse($data);
    }

    public function searchContactsAction(Request $request, $objectId): Response
    {
        $model = $this->getModel('event');
        /** @var \MauticPlugin\MauticEventsBundle\Entity\Event|null $event */
        $event = $model->getEntity($objectId);
        if (null === $event) {
            return $this->notFound('event', $objectId);
        }

        $term  = (string) $request->query->get('q', '');
        if ('undefined' === $term || 'null' === $term) {
            $term = '';
        }
        $page       = $request->query->getInt('page', 1);
        $limit      = $request->query->getInt('limit', 10);
        $excludeIds = $this->doctrine->getRepository(EventContact::class)->getAttachedContactIds($event);
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
        if (!$this->security->isGranted('events:events:edit')) {
            return $this->accessDenied();
        }

        $model = $this->getModel('event');
        /** @var \MauticPlugin\MauticEventsBundle\Entity\Event|null $event */
        $event = $model->getEntity($objectId);
        if (null === $event) {
            return $this->notFound('event', $objectId);
        }

        $data = json_decode($request->getContent(), true);
        $ids  = $data['contactIds'] ?? null;
        if (!is_array($ids)) {
            return $this->badRequest('Invalid contactIds');
        }

        $model->attachContacts($event, array_map('intval', $ids));

        foreach ($ids as $id) {
            $this->dispatcher->dispatch(new GenericEvent(null, [
                'eventId'   => $event->getId(),
                'contactId' => (int) $id,
+               'actorId'   => $this->user->getId(),
                'action'    => 'attach',
            ]), 'plugin.events.contact');
        }

        return new JsonResponse(['success' => true]);
    }

    public function detachContactAction(Request $request, $objectId, $contactId): Response
    {
        if (!$this->security->isGranted('events:events:edit')) {
            return $this->accessDenied();
        }

        $model = $this->getModel('event');
        /** @var \MauticPlugin\MauticEventsBundle\Entity\Event|null $event */
        $event = $model->getEntity($objectId);
        if (null === $event) {
            return $this->notFound('event', $objectId);
        }

        $model->detachContact($event, (int) $contactId);

        $this->dispatcher->dispatch(new GenericEvent(null, [
            'eventId'   => $event->getId(),
            'contactId' => (int) $contactId,
            'actorId'   => $this->user->getId(),
            'action'    => 'detach',
        ]), 'plugin.events.contact');

        return new JsonResponse(['success' => true]);
    }
}

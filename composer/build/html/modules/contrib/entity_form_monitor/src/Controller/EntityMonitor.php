<?php

namespace Drupal\entity_form_monitor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Defines a controller to find the latest update values of entities.
 */
class EntityMonitor extends ControllerBase {

  /**
   * Returns the entity updates.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JsonResponse object.
   */
  public function getUpdates(Request $request) {
    $post = $request->request->all();

    if (empty($post['entity_ids']) || !is_array($post['entity_ids'])) {
      throw new AccessDeniedHttpException();
    }

    $updates = [];
    foreach ($post['entity_ids'] as $entity_id) {
      // The entity ID is a combination of entity-type:entity-id.
      // @see _entity_form_monitor_process_entity_form()
      list($entity_type, $id) = explode(':', $entity_id);
      // @todo Should we be using loadUnchanged() here to ensure we have the absolute latest data?
      if ($entity = $this->entityTypeManager()->getStorage($entity_type)->load($id)) {
        // Check that the user has access to update the entity.
        if (!$entity->access('update')) {
          throw new AccessDeniedHttpException();
        }
        $updates[$entity_id] = $entity->getChangedTime();
      }
      else {
        // Entity was deleted.
        $updates[$entity_id] = FALSE;
      }
    }

    return new JsonResponse($updates);
  }

}

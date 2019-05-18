<?php

namespace Drupal\claims\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;

/**
 * Class ClaimVoucherController.
 */
class ClaimVoucherController extends ControllerBase {

  /**
   * Drupal\user\PrivateTempStoreFactory definition.
   *
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $userPrivateTempstore;
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ClaimVoucherController object.
   */
  public function __construct(PrivateTempStoreFactory $user_private_tempstore, EntityTypeManagerInterface $entity_type_manager) {
    $this->userPrivateTempstore = $user_private_tempstore;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Display.
   *
   * @return string
   *   Return Hello string.
   */
  public function display() {
    $tempstore = $this->userPrivateTempstore->get('claims');
    $partner_nid = $tempstore->get('partner');
    $partner_node = $this->entityTypeManager->getStorage('node')->load($partner_nid);
    $node = $this->entityTypeManager->getViewBuilder('node')->view($partner_node);

    return $node;
    // $rendered = \Drupal::service('renderer')->renderRoot($node);

    // return [
    //   '#theme' => 'claim_voucher',
    //   '#node' => $node['#node'],
    // ];
  }

  /**
   * Saves the data in user tempstore and redirects to claim voucher page
   *
   * @return AjaxResponse
   *
   */
  public function link($nid) {
    // Get user private temp store
    $tempstore = $this->userPrivateTempstore->get('claims');

    // Set the partner nid to store for showing on claim voucher page
    $tempstore->set('partner', $nid);

    $response = new AjaxResponse();
    $response->addCommand(new RedirectCommand('/claim-voucher'));

    return $response;
  }

}

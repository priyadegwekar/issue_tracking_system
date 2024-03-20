<?php

namespace Drupal\issue_tracking_system\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a 'LatestAssignedIssuesBlock' block.
 *
 * @Block(
 *   id = "latest_assigned_issues_block",
 *   admin_label = @Translation("Latest Assigned Issues Block"),
 *   category = @Translation("Custom")
 * )
 */
class LatestAssignedIssuesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new LatestAssignedIssuesBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $current_user = \Drupal::currentUser();
    $issues = $this->getAssignedIssues($current_user);

    if (!empty($issues)) {
      $build['#theme'] = 'item_list';
      $build['#items'] = [];

      foreach ($issues as $issue) {
        $build['#items'][] = $issue->getTitle();
      }
      $build['#cache']['contexts'][] = 'user:' . $current_user->id();

    }
    else {
      $build['#markup'] = $this->t('No assigned issues found.');
    }

    return $build;
  }

  /**
   * Retrieves the latest 3 issues assigned to the current user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\node\NodeInterface[]
   *   An array of node objects representing the latest 3 assigned issues.
   */
  protected function getAssignedIssues(AccountInterface $account) {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
    ->condition('type', 'issue')
    ->condition('field_assignee', $account->id())
    ->sort('created', 'DESC')
    ->range(0, 3);

    $nids = $query->execute();
    return $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    return $form;
  }

}

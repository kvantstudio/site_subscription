<?php

namespace Drupal\site_subscription\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a 'Subscription' Block
 *
 * @Block(
 *   id = "site_subscription_block",
 *   admin_label = @Translation("Subscription"),
 * )
 */
class SubscriptionBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        return \Drupal::formBuilder()->getForm('Drupal\site_subscription\Form\SubscriptionForm');
    }

    /**
     * {@inheritdoc}
     */
    protected function blockAccess(AccountInterface $account) {
        return AccessResult::allowedIfHasPermission($account, 'access content');
    }
}
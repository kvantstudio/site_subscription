<?php

/**
 * @file
 * Contains \Drupal\site_subscription\Form\SubscriptionSettingsForm
 */

namespace Drupal\site_subscription\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Subscription settings form.
 */
class SubscriptionSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'site_subscription_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'site_subscription.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Загружаем конфигурацию.
    $config = $this->config('site_subscription.settings');

    // Объявляет поле количество писем для обработки за один запуск crone.
    $form['number'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Number of messages to be sent at the start Crone'),
      '#description' => $this->t('The recommended value is 10.'),
      '#default_value' => $config->get('number') ? $config->get('number') : 10,
    );

    $form['clear_cron_count'] = array(
      '#title' => $this->t('Reset the counter of the number of emails to be sent when you start Crone'),
      '#type' => 'checkbox',
      '#default_value' => 0,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Записывает значения в конфигурацию.
    $config = \Drupal::service('config.factory')->getEditable('site_subscription.settings');
    $config->set('number', $form_state->getValue('number'));
    $config->save();

    // Сбросить счетчик количества писем для отправки при запуске Crone.
    if ($form_state->getValue('clear_cron_count')) {
      \Drupal::state()->set('site_subscription_count', 0);
    }
  }
}
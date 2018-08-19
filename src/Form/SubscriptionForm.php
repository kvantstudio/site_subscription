<?php

/**
 * @file
 * Contains \Drupal\site_subscription\Form\SubscriptionForm.
 */

namespace Drupal\site_subscription\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Subscription form.
 */
class SubscriptionForm extends FormBase {
    /**
     * {@inheritdoc}.
     */
    public function getFormId() {
        return 'site_subscription_form';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $account = \Drupal::currentUser();

        // Объявляем e-mail.
        $form['mail'] = array(
            '#type' => 'email',
            '#title' => $this->t('Enter your e-mail'),
            '#description' => '',
            '#attributes' => array('placeholder' => 'ivanov@yandex.ru'),
            '#access' => $account->id() ? FALSE : TRUE,
        );

        // Добавляем нашу кнопку для отправки.
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Subscribe'),
            '#button_type' => 'primary',
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $account = \Drupal::currentUser();
        
        if (!$account->id()) {
            $mail = trim($form_state->getValue('mail'));
            if (!$mail) {
                $form_state->setErrorByName('mail', $this->t('You have not specified e-mail.'));
            }
            
            if (!\Drupal::service('email.validator')->isValid($mail)) {
                $form_state->setErrorByName('mail', $this->t('You do not specify the correct e-mail.'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $account = \Drupal::currentUser();
        $connection = \Drupal::database();

        if ($account->id()) {
            $result = $connection->select('site_subscription', 'n')
                ->condition('n.uid', $account->id())
                ->condition('n.type', 'all')
                ->condition('n.type_id', 0);
            $count = $result->countQuery()->execute()->fetchField();            
        } else {
            $result = $connection->select('site_subscription', 'n')
                ->condition('n.mail', $form_state->getValue('mail'))
                ->condition('n.type', 'all')
                ->condition('n.type_id', 0);
            $count = $result->countQuery()->execute()->fetchField();
        }

        if (!$count) {
            $connection->insert('site_subscription')
                ->fields([
                    'uid' => $account->id(),
                    'type' => 'all',
                    'type_id' => '0',
                    'mail' => $account->id() ? $account->getEmail() : $form_state->getValue('mail'),
                    'created' => REQUEST_TIME,
                ])
                ->execute();
        } else {
            drupal_set_message($this->t('You are subscribed'));
        }
    }
}
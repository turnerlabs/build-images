<?php

namespace Drupal\entity_form_monitor\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Entity Form Monitor settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_form_monitor_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'entity_form_monitor.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('entity_form_monitor.settings');

    $form['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Interval between monitoring checks'),
      '#default_value' => $config->get('interval'),
      '#min' => 1,
      '#size' => 5,
      '#field_suffix' => $this->t('seconds'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('entity_form_monitor.settings')
      ->set('interval', $values['interval'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}

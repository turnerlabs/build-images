<?php

/**
 * @file
 */

namespace Drupal\tablefield\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Unicode;
/**
 *
 */
class TablefieldConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'tablefield_config_form';
  }

  /**
   *
   */
  protected function getEditableConfigNames() {
    return ['tablefield.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['csv_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CSV separator'),
      '#size' => 1,
      '#maxlength' => 1,
      '#default_value' => \Drupal::config('tablefield.settings')->get('csv_separator'),
      '#description' => $this->t('Select the separator for the CSV import/export.'),
    ];

    $form['rows'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default number of table rows'),
      '#size' => 3,
      '#maxlength' => 3,
      '#default_value' => \Drupal::config('tablefield.settings')->get('rows'),
      '#description' => $this->t('You can override this in field settings or in your custom form element.'),
    ];

    $form['cols'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default number of table columns'),
      '#size' => 2,
      '#maxlength' => 2,
      '#default_value' => \Drupal::config('tablefield.settings')->get('cols'),
      '#description' => $this->t('You can override this in field settings or in your custom form element.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (Unicode::strlen($form_state->getValue('csv_separator')) !== 1) {
      $message = $this->t('Separator must be one character only!');
      $this->setFormError('csv_separator', $message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('tablefield.settings')
      ->set('csv_separator', $form_state->getValue('csv_separator'))
      ->set('rows', $form_state->getValue('rows'))
      ->set('cols', $form_state->getValue('cols'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}

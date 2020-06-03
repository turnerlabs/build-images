<?php

/**
 * @file
 */

namespace Drupal\tablefield\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;


/**
 * Plugin implementation of the 'tablefield' widget.
 *
 * @FieldWidget (
 *   id = "tablefield",
 *   label = @Translation("Table Field"),
 *   field_types = {
 *     "tablefield"
 *   },
 * )
 */
class TablefieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'input_type' => 'textfield',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['input_type'] = [
      '#type' => 'radios',
      '#title' => t('Input type'),
      '#default_value' => $this->getSetting('input_type'),
      '#required' => TRUE,
      '#options' => [
        'textfield' => 'textfield',
        'textarea' => 'textarea',
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = t('Using input type: @input_type', ['@input_type' => $this->getSetting('input_type')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $is_field_settings_default_widget_form = $form_state->getBuildInfo()['form_id'] == 'field_ui_field_edit_form' ? 1 : 0;

    $field = $items[0]->getFieldDefinition();
    $field_settings = $field->getSettings();
    $field_widget_default = $field->getDefaultValueLiteral();

    if (!empty($field_widget_default[$delta])) {
      $field_default = (object) $field_widget_default[$delta];
    }

    if (isset($items[$delta]->value)) {
      $default_value = $items[$delta];
    }
    elseif (!$is_field_settings_default_widget_form && !empty($field_default)) {
      // Load field settings defaults in case current item is empty.
      $default_value = $field_default;
    }
    else {
      $default_value = (object) ['value' => [], 'rebuild' => []];
    }

    // Make sure rows and cols are set.
    $rows = isset($default_value->rebuild['rows']) ?
      $default_value->rebuild['rows'] : \Drupal::config('tablefield.settings')->get('rows');

    $cols = isset($default_value->rebuild['cols']) ?
      $default_value->rebuild['cols'] : \Drupal::config('tablefield.settings')->get('cols');

    $element = [
      '#type' => 'tablefield',
      '#input_type' => $this->getSetting('input_type'),
      '#description_display' => 'before',
      '#description' => $this->t('The first row will appear as the table header. Leave the first row blank if you do not need a header.'),
      '#cols' => $cols,
      '#rows' => $rows,
      '#default_value' => $default_value->value,
      '#lock' => !$is_field_settings_default_widget_form && $field_settings['lock_values'],
      '#locked_cells' => !empty($field_default->value) ? $field_default->value : [],
      '#rebuild' => \Drupal::currentUser()->hasPermission('rebuild tablefield'),
      '#import' => \Drupal::currentUser()->hasPermission('import tablefield'),
    ] + $element;

    if ($is_field_settings_default_widget_form) {
      $element['#description'] = $this->t('This form defines the table field defaults, but the number of rows/columns and content can be overridden on a per-node basis. The first row will appear as the table header. Leave the first row blank if you do not need a header.');
    }

    $element['#element_validate'][] = [$this, 'validateTablefield'];

    // Allow the user to select input filters.
    if (!empty($field_settings['cell_processing'])) {
      $element['#base_type'] = $element['#type'];
      $element['#type'] = 'text_format';
      $element['#format'] = isset($default_value->format) ? $default_value->format : NULL;
      $element['#editor'] = FALSE;
    }

    return $element;
  }

  /**
   *
   */
  public function validateTablefield(array &$element, FormStateInterface &$form_state, array $form) {
    if ($element['#required'] && $form_state->getTriggeringElement()['#type'] == 'submit') {
      $items = new FieldItemList($this->fieldDefinition);
      $this->extractFormValues($items, $form, $form_state);
      if (!$items->count()) {
        $form_state->setError($element, t('!name field is required.', ['!name' => $this->fieldDefinition->getLabel()]));
      }
    }
  }

  /**
   * {@inheritdoc}
   * set error only on the first item in a multi-valued field.
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return $element[0];
  }

}

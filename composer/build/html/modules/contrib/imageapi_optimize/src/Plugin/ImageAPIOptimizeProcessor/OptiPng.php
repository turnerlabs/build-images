<?php

namespace Drupal\imageapi_optimize\Plugin\ImageAPIOptimizeProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\imageapi_optimize\ImageAPIOptimizeProcessorBinaryBase;

/**
 * Uses the OptiPng binary to optimize images.
 *
 * @ImageAPIOptimizeProcessor(
 *   id = "optipng",
 *   label = @Translation("OptiPng"),
 *   description = @Translation("Uses the OptiPng binary to optimize images.")
 * )
 */
class OptiPng extends ImageAPIOptimizeProcessorBinaryBase {

  /**
   * {@inheritdoc}
   */
  protected function executableName() {
    return 'optipng';
  }

  public function applyToImage($image_uri) {
    if ($cmd = $this->getFullPathToBinary()) {
      $dst = $this->sanitizeFilename($image_uri);

      if ($this->getMimeType($image_uri) == 'image/png') {
        $options = array(
          '--quiet',
        );
        $arguments = array(
          $dst,
        );

        if (is_numeric($this->configuration['interlace'])) {
          $options[] = '-i ' . $this->configuration['interlace'];
        }

        $options[] = '-o' . $this->configuration['level'];

        return $this->execShellCommand($cmd, $options, $arguments);
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'level' => 5,
      'interlace' => '',
    ];
  }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['level'] = array(
      '#title' => t('Optimization level'),
      '#type' => 'select',
      '#options' => range(0, 7),
      '#default_value' => $this->configuration['level'],
    );

    $form['interlace'] = array(
      '#title' => t('Interlace'),
      '#type' => 'select',
      '#options' => array(
        '' => t('No change'),
        0 => t('Non-interlaced'),
        1 => t('Interlaced'),
      ),
      '#default_value' => $this->configuration['interlace'],
      '#description' => t('If "No change" is select, the output will have the same interlace type as the input.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['level'] = $form_state->getValue('level');
    $this->configuration['interlace'] = $form_state->getValue('interlace');
  }
}

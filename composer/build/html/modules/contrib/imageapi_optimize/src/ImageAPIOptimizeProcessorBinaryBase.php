<?php

namespace Drupal\imageapi_optimize;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\Core\Image\ImageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for image optimizations that run local binaries.
 */
abstract class ImageAPIOptimizeProcessorBinaryBase extends ConfigurableImageAPIOptimizeProcessorBase {

  /**
   * The file system service.
   *
   * @var \Drupal\core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerInterface $logger, ImageFactory $image_factory, FileSystemInterface $file_system) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger, $image_factory);

    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('imageapi_optimize'),
      $container->get('image.factory'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'manual_executable_path' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    if (!$this->findExecutablePath()) {
      $form['executable'] = array(
        '#type' => 'item',
        '#title' => t('Executable'),
        '#markup' => t('The @binary binary must be installed, please install or specify the path to the @binary executable directly.', array('@binary' => $this->executableName())),
      );
    }
    else {
      $form['executable'] = array(
        '#type' => 'item',
        '#title' => t('Executable'),
        '#markup' => t('The @binary executable has been automatically located: @path. To override, set a different executate path below.', array('@path' => $this->findExecutablePath(), '@binary' => $this->executableName())),
      );
    }

    $form['manual_executable_path'] = array(
      '#type' => 'textfield',
      '#title' => t('Manually set path'),
      '#description' => t('Specify the full path to the @binary executable.', array('@binary' => $this->executableName())),
      '#default_value' => $this->configuration['manual_executable_path'],
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['manual_executable_path'] = $form_state->getValue('manual_executable_path');
  }

  /**
   * Search the local system for the given executable binary.
   *
   * @param null $executable
   *   The name of the executable binary to find on the local system. If not
   *   specified the default executeable name for this class will be used.
   *
   * @return string|false
   *   The path to the binary on the local system, or FALSE if it could not be
   *   located.
   */
  protected function findExecutablePath($executable = NULL) {
    if (is_null($executable)) {
      $executable = $this->executableName();
    }
    $output = array();
    $return_var = 0;
    $path = exec('which ' . escapeshellarg($executable), $output, $return_var);
    if ($return_var == 0) {
      return $path;
    }
    return FALSE;
  }

  /**
   * Execute a shell command on the local system.
   *
   * @param $command
   *   The command to execute.
   * @param $options
   *   An array of options for the command. This will not be escaped before executing.
   * @param $arguments
   *   An array of arguments for the command. These will be escaped.
   *
   * @return bool
   *   Returns TRUE if the command completed successfully, FALSE otherwise.
   */
  protected function execShellCommand($command, $options, $arguments) {
    $output = array();
    $return_val = 0;
    $option_string = implode(' ', $options);
    $argument_string = implode(' ', array_map('escapeshellarg', $arguments));
    $last_line = exec(escapeshellcmd($command) . ' ' . $option_string . ' ' . $argument_string, $output, $return_val);

    if ($return_val == 0) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Sanitize the given path for binary processors.
   *
   * @param $drupal_filepath
   *   The file path to sanitize.
   *
   * @return string
   *   The sanitized file path.
   */
  protected function sanitizeFilename($drupal_filepath) {
    return $this->fileSystem->realpath($drupal_filepath);
  }

  protected function saveCommandStdoutToFile($cmd, $dst) {
    $return_val = 0;
    ob_start();
    passthru($cmd);
    $output = ob_get_contents();
    ob_end_clean();

    file_unmanaged_save_data($output, $dst, FILE_EXISTS_REPLACE);

    if ($return_val == 0) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function getFullPathToBinary() {
    if ($this->configuration['manual_executable_path']) {
      return $this->configuration['manual_executable_path'];
    }
    elseif ($this->findExecutablePath()) {
      return $this->findExecutablePath();
    }
  }

  public function getSummary() {
    $description = '';

    if (!$this->getFullPathToBinary()) {
      $description .= t('<strong>Command not found</strong>');
    }

    $summary = array(
      '#markup' => $description,
    );
    $summary += parent::getSummary();

    return $summary;
  }

  abstract protected function executableName();

  /**
   * {@inheritdoc}
   */
  abstract public function applyToImage($image_uri);

}

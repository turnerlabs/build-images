<?php

namespace Drupal\imageapi_optimize;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages image optimize processor plugins.
 *
 * @see hook_imageapi_optimize_processor_info_alter()
 * @see \Drupal\imageapi_optimize\Annotation\ImageAPIOptimizeProcessor
 * @see \Drupal\imageapi_optimize\ConfigurableImageAPIOptimizeProcessorInterface
 * @see \Drupal\imageapi_optimize\ConfigurableImageAPIOptimizeProcessorBase
 * @see \Drupal\imageapi_optimize\ImageAPIOptimizeProcessorInterface
 * @see \Drupal\imageapi_optimize\ImageAPIOptimizeProcessorBase
 * @see plugin_api
 */
class ImageAPIOptimizeProcessorManager extends DefaultPluginManager {

  /**
   * Constructs a new ImageAPIOptimizeProcessorManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ImageAPIOptimizeProcessor', $namespaces, $module_handler, 'Drupal\imageapi_optimize\ImageAPIOptimizeProcessorInterface', 'Drupal\imageapi_optimize\Annotation\ImageAPIOptimizeProcessor');

    $this->alterInfo('imageapi_optimize_processor_info');
    $this->setCacheBackend($cache_backend, 'imageapi_optimize_processor_plugins');
  }

}

<?php

namespace Drupal\twig_temp;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Template\TwigEnvironment as CoreTwigEnvironment;
use Drupal\Core\State\StateInterface;

/**
 * Twig environment that uses temporary file storage.
 */
class TwigEnvironment extends CoreTwigEnvironment {

  /**
   * Constructs a TwigEnvironment object and stores cache and storage in temp.
   *
   * @param string $root
   *   The app root.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache bin.
   * @param string $twig_extension_hash
   *   The Twig extension hash.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Twig_LoaderInterface $loader
   *   The Twig loader or loader chain.
   * @param array $options
   *   The options for the Twig environment.
   */
  public function __construct($root, CacheBackendInterface $cache, $twig_extension_hash, StateInterface $state, \Twig_LoaderInterface $loader = NULL, $options = []) {
    // Only enable the cache if it's enabled.
    if (isset($options['cache']) && $options['cache']) {
      $options = [
        'cache' => new TwigTemporaryPhpStorageCache($cache, $twig_extension_hash),
      ];
    }

    parent::__construct($root, $cache, $twig_extension_hash, $state, $loader, $options);
  }

}

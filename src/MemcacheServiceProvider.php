<?php

namespace Drupal\memcache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * When Memcache module is enabled, set the backend cache to memcache.
 */
class MemcacheServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('cache.backend.chainedfast');
    $arguments = $definition->getArguments();
    $arguments[1] = 'cache.backend.memcache';
    $definition->setArguments($arguments);
    $container->setDefinition('cache.backend.chainedfast', $definition);
  }
}

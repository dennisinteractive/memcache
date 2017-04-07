<?php

/**
 * @file
 * Contains \Drupal\memcache\DrupalMemcacheBase.
 */

namespace Drupal\memcache;

use Psr\Log\LogLevel;

/**
 * Class DrupalMemcacheBase.
 */
abstract class DrupalMemcacheBase implements DrupalMemcacheInterface {

  use MemcacheCacheNormalizer;

  /**
   * The memcache config object.
   *
   * @var \Drupal\memcache\DrupalMemcacheConfig
   */
  protected $settings;

  /**
   * The memcache object.
   *
   * @var mixed
   *   E.g. \Memcache|\Memcached
   */
  protected $memcache;

  /**
   * The hash algorithm to pass to hash(). Defaults to 'sha1'
   *
   * @var string
   */
  protected $hashAlgorithm;

  /**
   * The prefix memcache key for all keys.
   *
   * @var string
   */
  protected $prefix;

  /**
   * Constructs a DrupalMemcacheBase object.
   *
   * @param \Drupal\memcache\DrupalMemcacheConfig
   *   The memcache config object.
   */
  public function __construct(DrupalMemcacheConfig $settings) {
    $this->settings = $settings;

    $this->hashAlgorithm = $this->settings->get('key_hash_algorithm', 'sha1');
    $this->prefix = $this->settings->get('key_prefix', '');
  }

  /**
   * {@inheritdoc}
   */
  public function get($key) {
    $full_key = $this->key($key);

    $track_errors = ini_set('track_errors', '1');
    $php_errormsg = '';
    $result = @$this->memcache->get($full_key);

    if (!empty($php_errormsg)) {
      register_shutdown_function('memcache_log_warning', LogLevel::WARNING, 'Exception caught in DrupalMemcacheBase::get: !msg', array('!msg' => $php_errormsg));
      $php_errormsg = '';
    }
    ini_set('track_errors', $track_errors);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function key($key) {
    return $this->normalizeKey($this->prefix . '-' . $key);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($key) {
    $full_key = $this->key($key);
    return $this->memcache->delete($full_key, 0);
  }

  /**
   * {@inheritdoc}
   */
  public function flush() {
    $this->memcache->flush();
  }

}

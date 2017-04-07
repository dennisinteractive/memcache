<?php

namespace Drupal\memcache;

/**
 * Class MemcacheCacheNormalizer.
 */
trait MemcacheCacheNormalizer {

  /**
   * Normalizes a cache ID in order to comply with key length limitations.
   *
   * @param string $key
   *   The passed in cache ID.
   *
   * @return string
   *   An ASCII-encoded cache ID that is at most 250 characters long.
   */
  protected function normalizeKey($key) {
    $key = urlencode($key);
    // Nothing to do if the ID is a US ASCII string of 250 characters or less.
    $key_is_ascii = mb_check_encoding($key, 'ASCII');
    if (strlen($key) <= 250 && $key_is_ascii) {
      return $key;
    }
    // Memcache only supports key lengths up to 250 bytes.  If we have generated
    // a longer key, we shrink it to an acceptable length with a configurable
    // hashing algorithm. Sha1 was selected as the default as it performs
    // quickly with minimal collisions.
    // Return a string that uses as much as possible of the original cache ID
    // with the hash appended.
    $hash_algorithm = $this->settings->get('key_hash_algorithm', 'sha1');
    $hash = hash($hash_algorithm, $key);
    if (!$key_is_ascii) {
      return $hash;
    }
    return substr($key, 0, 250 - strlen($hash)) . $hash;
  }

}

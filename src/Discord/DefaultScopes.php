<?php
namespace Discord\OAuth;

class DefaultScopes {

  public static $defaultScopes;

  public static function setDefaultScopes($array) {
    self::$defaultScopes = $array;
  }
}

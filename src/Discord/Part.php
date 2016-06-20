<?php

/*
 * This file is a part of the oauth2-discord project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\OAuth;

use League\OAuth2\Client\Token\AccessToken;

class Part
{
    /**
     * The Discord OAuth provider.
     *
     * @var Discord OAuth provider.
     */
    protected $discord;

    /**
     * The access token.
     *
     * @var AccessToken Access token.
     */
    protected $token;

    /**
     * The fillable attributes.
     *
     * @var array Fillable attributes.
     */
    protected $fillable = [];

    /**
     * The array of attributes.
     *
     * @var array Attributes.
     */
    protected $attributes = [];

    /**
     * Constructs a part.
     *
     * @param array $attributes Attributes to set.
     */
    public function __construct(Discord $discord, AccessToken $token, array $attributes = [])
    {
        $this->discord = $discord;
        $this->token = $token;

        $this->fill($attributes);
    }

    /**
     * Fills the part with attributes.
     *
     * @param array $attributes Attributes to set.
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (array_search($key, $this->fillable) !== false) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Handles dynamic get calls to the part.
     *
     * @param string $variable The variable that was got.
     *
     * @return mixed
     */
    public function __get($variable)
    {
        $func = 'get'.ucfirst($variable).'Attribute';

        if (is_callable([$this, $func])) {
            return $this->{$func}();
        }

        if (array_key_exists($variable, $this->attributes)) {
            return $this->attributes[$variable];
        }
    }

    /**
     * Handles dynamic set calls to the part.
     *
     * @param string $variable The variable to set.
     * @param mixed  $value    The value to set.
     */
    public function __set($variable, $value)
    {
        if (array_search($variable, $this->fillable) !== false) {
            $this->attributes[$variable] = $value;
        }
    }

    /**
     * Handles debug calls.
     *
     * @return array Debug info.
     *
     * @codeCoverageIgnore
     */
    public function __debugInfo()
    {
        return $this->toArray();
    }

    /**
     * Converts the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];

        foreach ($this->fillable as $key) {
            $array[$key] = $this->{$key};
        }

        return $array;
    }
}

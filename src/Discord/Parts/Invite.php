<?php

/*
 * This file is a part of the discord-oauth project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\OAuth\Parts;

use Discord\OAuth\Part;

class Invite extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'code',
        'max_age',
        'guild',
        'revoked',
        'created_at',
        'temporary',
        'uses',
        'max_uses',
        'inviter',
        'xkcdpass',
        'channel',
    ];
}

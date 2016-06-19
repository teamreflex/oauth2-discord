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

use Discord\OAuth\Discord;
use Discord\OAuth\Part;
use Illuminate\Support\Collection;

class User extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'username',
        'email',
        'discriminator',
        'avatar',
        'verified',
        'mfa_enabled',
    ];

    /**
     * Gets the guilds attribute.
     *
     * @return Collection Guilds.
     */
    public function getGuildsAttribute()
    {
        $request = $this->discord->getAuthenticatedRequest(
            'GET',
            Discord::BASE_API_URL.'/users/@me/guilds',
            $this->token
        );
        $response = $this->discord->getResponse($request);

        $collection = new Collection();

        foreach ((array) $response as $raw) {
            $collection->push(new Guild(
                $this->discord,
                $this->token,
                (array) $raw
            ));
        }

        return $collection;
    }

    /**
     * Gets the connections attribute.
     *
     * @return Collection Connections.
     */
    public function getConnectionsAttribute()
    {
        $request = $this->discord->getAuthenticatedRequest(
            'GET',
            Discord::BASE_API_URL.'/users/@me/connections',
            $this->token
        );
        $response = $this->discord->getResponse($request);

        $collection = new Collection();

        foreach ((array) $response as $raw) {
            $collection->push(new Connection(
                $this->discord,
                $this->token,
                (array) $raw
            ));
        }

        return $collection;
    }

    /**
     * Accepts an invite.
     *
     * @param string $invite The invite code or URL.
     *
     * @return Invite Accepted invite.
     */
    public function acceptInvite($invite)
    {
        if (preg_match('/https:\/\/discord.gg\/(.+)/', $invite, $matches)) {
            $invite = $matches[1];
        }

        $request = $this->discord->getAuthenticatedRequest(
            'POST',
            Discord::BASE_API_URL.'/invites/'.$invite,
            $this->token
        );
        $response = $this->discord->getResponse($request);

        return new Invite(
            $this->discord,
            $this->token,
            $response
        );
    }
}

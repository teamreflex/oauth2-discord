<?php

/*
 * This file is a part of the oauth2-discord project.
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
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class User extends Part implements ResourceOwnerInterface
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
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the guilds attribute.
     *
     * @return Collection Guilds.
     */
    public function getGuildsAttribute()
    {
        $response = $this->discord->request(
            'GET',
            $this->discord->getGuildsEndpoint(),
            $this->token
        );

        $collection = new Collection();

        foreach ($response as $raw) {
            $collection->push(
                $this->discord->buildPart(Guild::class, $this->token, $raw)
            );
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
        $response = $this->discord->request(
            'GET',
            $this->discord->getConnectionsEndpoint(),
            $this->token
        );

        $collection = new Collection();

        foreach ($response as $raw) {
            $collection->push(
                $this->discord->buildPart(Connection::class, $this->token, $raw)
            );
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

        $response = $this->discord->request(
            'POST',
            $this->discord->getInviteEndpoint($invite),
            $this->token
        );

        return $this->discord->buildPart(Invite::class, $this->token, $response);
    }
}

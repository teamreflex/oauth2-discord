<?php

/*
 * This file is a part of the oauth2-discord project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\OAuth\tests;

use Discord\OAuth\Discord as DiscordProvider;
use GuzzleHttp\Psr7\Response;
use Mockery as m;

class DiscordTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;
    protected $config = [
        'clientId' => 'mock_id',
        'clientSecret' => 'mock_secret',
    ];

    public function setUp()
    {
        $this->provider = new DiscordProvider($this->config);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertArrayHasKey('client_id', $query);

        $this->assertEquals($this->config['clientId'], $query['client_id']);

        $this->assertContains('identify', $query['scope']);
        $this->assertContains('email', $query['scope']);

        $this->assertAttributeNotEmpty('state', $this->provider);
    }

    public function testBaseAccessTokenUrl()
    {
        $url = $this->provider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);

        $this->assertEquals('/api/oauth2/token', $uri['path']);
    }

    public function testResourceOwnerDetailsUrl()
    {
        $token = m::mock('League\OAuth2\Client\Token\AccessToken', [['access_token' => 'mock_access_token']]);

        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        $uri = parse_url($url);

        $this->assertEquals('/api/users/@me', $uri['path']);
        $this->assertNotContains('mock_access_token', $url);
    }

    public function testUserData()
    {
        $response = json_decode('{"id": "mock_id", "username": "mock_username", "email": "mock_email", "discriminator": "mock_discrim", "avatar": "mock_avatar", "verified": true, "mfa_enabled": false}', true);

        $provider = m::mock(DiscordProvider::class.'[fetchResourceOwnerDetails]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('fetchResourceOwnerDetails')
            ->times(1)
            ->andReturn($response);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');
        $user = $provider->getResourceOwner($token);

        $this->assertInstanceOf('League\OAuth2\Client\Provider\ResourceOwnerInterface', $user);

        $this->assertEquals('mock_id', $user->id);
        $this->assertEquals('mock_username', $user->username);
        $this->assertEquals('mock_email', $user->email);
        $this->assertEquals('mock_discrim', $user->discriminator);
        $this->assertEquals('mock_avatar', $user->avatar);
        $this->assertEquals(true, $user->verified);
        $this->assertEquals(false, $user->mfa_enabled);

        // tests for part
        $this->assertEquals(null, $user->mock_test_part);

        $user = $user->toArray();

        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('username', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayHasKey('discriminator', $user);
        $this->assertArrayHasKey('avatar', $user);
        $this->assertArrayHasKey('verified', $user);
        $this->assertArrayHasKey('mfa_enabled', $user);
    }

    public function testGuildData()
    {
        $userResponse = json_decode('{"id": "mock_id", "username": "mock_username", "email": "mock_email", "discriminator": "mock_discrim", "avatar": "mock_avatar", "verified": true, "mfa_enabled": false}', true);
        $response = json_decode('[{"id": "mock_id1", "name": "mock_name1", "icon": "mock_icon1", "permissions": 123123, "owner": false}, {"id": "mock_id2", "name": "mock_name2", "icon": "mock_icon2", "permissions": 456456, "owner": true}]', true);

        $provider = m::mock(DiscordProvider::class.'[fetchResourceOwnerDetails,request]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('fetchResourceOwnerDetails')
            ->times(1)
            ->andReturn($userResponse);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');
        $user = $provider->getResourceOwner($token);

        $provider->shouldReceive('request')
            ->times(1)
            ->andReturn($response);

        $guilds = $user->guilds;

        $this->assertEquals(2, $guilds->count());

        list($guild1, $guild2) = $guilds;

        $this->assertEquals('mock_id1', $guild1->id);
        $this->assertEquals('mock_name1', $guild1->name);
        $this->assertEquals('mock_icon1', $guild1->icon);
        $this->assertEquals(123123, $guild1->permissions);
        $this->assertEquals(false, $guild1->owner);

        $this->assertEquals('mock_id2', $guild2->id);
        $this->assertEquals('mock_name2', $guild2->name);
        $this->assertEquals('mock_icon2', $guild2->icon);
        $this->assertEquals(456456, $guild2->permissions);
        $this->assertEquals(true, $guild2->owner);
    }

    public function testConnectionData()
    {
        $userResponse = json_decode('{"id": "mock_id", "username": "mock_username", "email": "mock_email", "discriminator": "mock_discrim", "avatar": "mock_avatar", "verified": true, "mfa_enabled": false}', true);
        $response = json_decode('[{"id": "mock_id1", "name": "mock_name1", "type": "mock_type1"}, {"id": "mock_id2", "name": "mock_name2", "type": "mock_type2"}]', true);

        $provider = m::mock(DiscordProvider::class.'[fetchResourceOwnerDetails,request]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('fetchResourceOwnerDetails')
            ->times(1)
            ->andReturn($userResponse);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');
        $user = $provider->getResourceOwner($token);

        $provider->shouldReceive('request')
            ->times(1)
            ->andReturn($response);

        $connections = $user->connections;

        $this->assertEquals(2, $connections->count());

        list($conn1, $conn2) = $connections;

        $this->assertEquals('mock_id1', $conn1->id);
        $this->assertEquals('mock_name1', $conn1->name);
        $this->assertEquals('mock_type1', $conn1->type);

        $this->assertEquals('mock_id2', $conn2->id);
        $this->assertEquals('mock_name2', $conn2->name);
        $this->assertEquals('mock_type2', $conn2->type);

        $conn = $conn1->toArray();

        $this->assertArrayHasKey('id', $conn);
        $this->assertArrayHasKey('name', $conn);
        $this->assertArrayHasKey('type', $conn);
    }

    public function testInviteData()
    {
        $userResponse = json_decode('{"id": "mock_id", "username": "mock_username", "email": "mock_email", "discriminator": "mock_discrim", "avatar": "mock_avatar", "verified": true, "mfa_enabled": false}', true);
        $response = json_decode('{"code": "mock_code", "guild": {"id": "mock_id", "name": "mock_name", "splash_hash": "mock_splash_hash"}, "xkcdpass": null, "channel": {"id": "mock_id", "name": "mock_name", "type": "mock_type"}}', true);

        $provider = m::mock(DiscordProvider::class.'[fetchResourceOwnerDetails,request]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('fetchResourceOwnerDetails')
            ->times(1)
            ->andReturn($userResponse);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');
        $user = $provider->getResourceOwner($token);

        $provider->shouldReceive('request')
            ->times(2)
            ->andReturn($response);

        $invite = $user->acceptInvite('mock_code');
        $invite2 = $user->acceptInvite('https://discord.gg/mock_code');

        foreach ([$invite, $invite2] as $invite) {
            $this->assertEquals('mock_code', $invite->code);
            $this->assertEquals([
                'id' => 'mock_id',
                'name' => 'mock_name',
                'splash_hash' => 'mock_splash_hash',
            ], $invite->guild);
            $this->assertEquals(null, $invite->xkcdpass);
            $this->assertEquals([
                'id' => 'mock_id',
                'name' => 'mock_name',
                'type' => 'mock_type',
            ], $invite->channel);
        }

        $invite = $invite->toArray();

        $this->assertArrayHasKey('code', $invite);
        $this->assertArrayHasKey('guild', $invite);
        $this->assertArrayHasKey('xkcdpass', $invite);
        $this->assertArrayHasKey('channel', $invite);
    }

    public function testDiscordRequest()
    {
        $guzzleResponse = new Response(200, [], '{"mock": true}');

        $provider = m::mock(DiscordProvider::class.'[sendRequest]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('sendRequest')
            ->times(1)
            ->andReturn($guzzleResponse);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');

        $token->shouldReceive('getToken')
            ->times(1)
            ->andReturn('mock_token');

        $response = $provider->request(
            'GET',
            'mock_url',
            $token
        );

        $this->assertArrayHasKey('mock', $response);
        $this->assertEquals(true, $response['mock']);
    }

    /**
     * @expectedException Discord\OAuth\DiscordRequestException
     */
    public function testResponseChecking()
    {
        $guzzleResponse = new Response(500, [], '{"error": "mock_error"}');

        $provider = m::mock(DiscordProvider::class.'[sendRequest]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('sendRequest')
            ->times(1)
            ->andReturn($guzzleResponse);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');

        $token->shouldReceive('getToken')
            ->times(1)
            ->andReturn('mock_token');

        $response = $provider->request(
            'GET',
            'mock_url',
            $token
        );
    }
}

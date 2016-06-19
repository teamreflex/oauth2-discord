<?php

namespace Discord\OAuth\Tests;

use Mockery as m;
use Discord\OAuth\Discord as DiscordProvider;

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

        $user = $user->toArray();

        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('username', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayHasKey('discriminator', $user);
        $this->assertArrayHasKey('avatar', $user);
        $this->assertArrayHasKey('verified', $user);
        $this->assertArrayHasKey('mfa_enabled', $user);
	}
}
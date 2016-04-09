<?php

namespace Discord\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Discord extends AbstractProvider
{
	/**
	 * The base API url.
	 * 
	 * @var string Base API url.
	 */
	const BASE_API_URL = 'https://discordapp.com/api';

	/**
	 * An array of available OAuth scopes.
	 *
	 * @var array Available scopes.
	 */
	protected $scopes = [
		'identify', // Allows you to retrieve user data (except for email)
		'email', // The same as identify but with email
		'connections', // Allows you to retrieve connected YouTube and Twitch accounts
		'guilds', // Allows you to retrieve the guilds the user is in
		'guilds.join', // Allows you to join the guild for the user
		'bot', // Defines a bot
	];

	/**
	 * {@inheritdoc}
	 */
	public function getBaseAuthorizationUrl()
	{
		return self::BASE_API_URL.'/oauth2/authorize';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBaseAccessTokenUrl(array $params)
	{
		return self::BASE_API_URL.'/oauth2/token';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResourceOwnerDetailsUrl(AccessToken $token)
	{
		return self::BASE_API_URL.'/users/@me';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultScopes()
	{
		return ['identify', 'email'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getScopeSeparator()
	{
		return ' ';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function checkResponse(ResponseInterface $response, $data)
	{
		
	}

	/**
	 * {@inheritdoc}
	 */
	protected function createResourceOwner(array $response, AccessToken $token)
	{

	}
}

oauth2-discord
===
[![Build Status](https://travis-ci.org/teamreflex/oauth2-discord.svg?branch=master)](https://travis-ci.org/teamreflex/oauth2-discord) [![Coverage Status](https://coveralls.io/repos/github/teamreflex/oauth2-discord/badge.svg?branch=master)](https://coveralls.io/github/teamreflex/oauth2-discord?branch=master)

Provides Discord OAuth 2.0 support for PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

Run `composer require team-reflex/oauth2-discord`.

## Usage

```php
<?php

$provider = new \Discord\OAuth\Discord([
	'clientId'     => 'oauth-app-id',
	'clientSecret' => 'oauth-app-secret',
	'redirectUri'  => 'http://your.redirect.url',
]);

if (! isset($_GET['code'])) {
	echo '<a href="'.$provider->getAuthorizationUrl().'">Login with Discord</a>';
} else {
	$token = $provider->getAccessToken('authorization_code', [
		'code' => $_GET['code'],
	]);

	// Get the user object.
	$user = $provider->getResourceOwner($token);

	// Get the guilds and connections.
	$guilds = $user->guilds;
	$connections = $user->connections;

	// Accept an invite
	$invite = $user->acceptInvite('https://discord.gg/0SBTUU1wZTUo9F8v');

	// Get a refresh token
	$refresh = $provider->getAccessToken('refresh_token', [
		'refresh_token' => $getOldTokenFromMemory->getRefreshToken(),
	]);

	// Store the new token.
}
```

## Credits

- [David Cole](https://github.com/uniquoooo)
- [All Contributors](https://github.com/teamreflex/oauth2-discord/contributors)

## License

This code is subject to the MIT license which can be found in the [LICENSE](LICENSE) file.
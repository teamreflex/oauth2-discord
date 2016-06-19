discord-oauth
===
Provides Discord OAuth 2.0 support for PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

This is still under development and I don't know if half of it works yet.

## Installation

Run `composer require team-reflex/discord-oauth`.

## Usage

```php
<?php

$provider = new \Discord\OAuth\Discord([
	'clientId'     => 'oauth-app-id',
	'clientSecret' => 'oauth-app-secret',
]);

if (! isset($_GET['code'])) {
	echo '<a href="'.$provider->getAuthorizationUrl().">Login with Discord</a>';
} else {
	$token = $provider->getAccessToken('client_credentials', [
		'code' => $_GET['code'],
	]);

	// Get the user object.
	$user = $provider->getResourceOwner($token);

	// Get the guilds and connections.
	$guilds = $user->guilds;
	$connections = $user->connections;

	// Accept an invite
	$invite = $user->acceptInvite('https://discord.gg/0SBTUU1wZTUo9F8v');
}
```

## License

This code is subject to the MIT license which can be found in the [LICENSE](LICENSE) file.
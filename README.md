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

echo '<a href="'.$provider->getAuthorizationUrl().'">Login with Discord</a>';
```

## License

This code is subject to the MIT license which can be found in the [LICENSE](LICENSE) file.

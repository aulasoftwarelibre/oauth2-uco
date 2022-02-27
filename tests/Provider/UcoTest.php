<?php

/*
 * This file is part of the `aulasoftwarelibre/oauth2-client`.
 *
 * Copyleft (C) 2018 by Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\AulaSoftwareLibre\OAuth2\Client\Provider;

use AulaSoftwareLibre\OAuth2\Client\Provider\Uco;
use PHPUnit\Framework\TestCase;

class UcoTest extends TestCase
{
    /**
     * @var Uco
     */
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new Uco([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'mock_redirect_uri',
        ]);
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
    }
}

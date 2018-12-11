<?php

/*
 * This file is part of the `informaticauco/oauth2-client`.
 *
 * Copyright (C) 2018 by Sergio Gómez <sergio@uco.es>
 *
 * This code was developed by Universidad de Córdoba (UCO https://www.uco.es)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Uco\OAuth2\Client\Provider;

use Jose\Factory\JWKFactory;
use Jose\Loader;
use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Uco extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * {@inheritdoc}
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://identidad.uco.es/simplesaml/module.php/oidc/authorize.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://identidad.uco.es/simplesaml/module.php/oidc/access_token.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://identidad.uco.es/simplesaml/module.php/oidc/userinfo.php';
    }

    public function getBaseJsonWebKeyUrl()
    {
        return 'https://identidad.uco.es/simplesaml/module.php/oidc/jwks.php';
    }

    /**
     * {@inheritdoc}
     */
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        $token = parent::createAccessToken($response, $grant);
        if (!array_key_exists('id_token', $token->getValues())) {
            throw new \InvalidArgumentException('Access token does not contains \'id_token\'.');
        }

        $jwks = JWKFactory::createFromJKU($this->getBaseJsonWebKeyUrl());
        $jwt = $token->getValues()['id_token'];

        $loader = new Loader();
        $loader->loadAndVerifySignatureUsingKeySet($jwt, $jwks, ['RS256']);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultScopes()
    {
        return ['openid'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * {@inheritdoc}
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $code = 0;
            $error = $data['error'];
            if (\is_array($error)) {
                $code = $error['code'];
                $error = $error['message'];
            }
            throw new IdentityProviderException($error, $code, $data);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new UcoResourceOwner($response);
    }
}

<?php

/*
 * This file is part of the `aulasoftwarelibre/oauth2-client`.
 *
 * Copyleft (C) 2018 by Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AulaSoftwareLibre\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

final class UcoResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    private $response;

    /**
     * Create new user resource owner.
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getValueByKey($this->response, 'sub');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->response;
    }
}

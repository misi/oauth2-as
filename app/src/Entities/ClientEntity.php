<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2Server\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait, ClientTrait;

    private $internalID;
    private $description;
    private $confidential;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;
    }

    public function setInternalID($internalID)
    {
        $this->internalID = $internalID;
    }

    public function getInternalID()
    {
        return $this->internalID;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setConfidential($confidential)
    {
        $this->confidential = $confidential;
    }

    public function getConfidential()
    {
        return $this->confidential;
    }

}

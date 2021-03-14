<?php

declare(strict_types=1);

namespace Pingen;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Class ResourceOwner
 * @package Pingen
 */
class ResourceOwner implements ResourceOwnerInterface
{
    protected string $id;

    protected string $fullName;

    protected string $email;

    /**
     * ResourceOwner constructor.
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->id = $response['data']['id'];
        $this->fullName = $response['data']['attributes']['first_name'] . ' ' . $response['data']['attributes']['last_name'];
        $this->email = $response['data']['attributes']['email'];
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->fullName,
        ];
    }
}

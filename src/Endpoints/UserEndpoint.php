<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Pingen\Endpoints\DataTransferObjects\User\UserDetails;
use Pingen\Endpoints\ParameterBags\UserParameterBag;
use Pingen\ResourceEndpoint;

/**
 * Class UserEndpoint
 * @package Pingen\Endpoints
 */
class UserEndpoint extends ResourceEndpoint
{
    /**
     * @param UserParameterBag|null $parameterBag
     * @return UserDetails
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getDetails(?UserParameterBag $parameterBag = null): UserDetails
    {
        return new UserDetails(
            $this->performGetDetailsRequest(
                '/user',
                $parameterBag ?? (new UserParameterBag())
            )->json()
        );
    }
}

<?php

declare(strict_types=1);

namespace Pingen\Endpoints;

use Illuminate\Http\Client\RequestException;
use Pingen\Endpoints\DataTransferObjects\Organisation\OrganisationDetails;
use Pingen\Endpoints\ParameterBags\OrganisationParameterBag;
use Pingen\ResourceEndpoint;

/**
 * Class OrganisationsEndpoint
 * @package Pingen\Endpoints
 */
class OrganisationsEndpoint extends ResourceEndpoint
{
    /**
     * @param string $organisationId
     * @param OrganisationParameterBag|null $parameterBag
     * @return OrganisationDetails
     * @throws RequestException
     */
    public function getDetails(string $organisationId, ?OrganisationParameterBag $parameterBag = null): OrganisationDetails
    {
        return new OrganisationDetails(
            $this->performGetDetailsRequest(
                sprintf('/organisations/%s', $organisationId),
                $parameterBag ?? (new OrganisationParameterBag())
            )->json()
        );
    }
}

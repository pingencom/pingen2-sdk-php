<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Organisation;

use Pingen\Support\DataTransferObject\DataTransferObject;

/**
 * Class OrganisationDetails
 * @package Pingen\DataTransferObjects\Organisation
 */
class OrganisationDetails extends DataTransferObject
{
    public OrganisationDetailsData $data;
}

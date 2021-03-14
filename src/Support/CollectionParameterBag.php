<?php

declare(strict_types=1);

namespace Pingen\Support;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CollectionParameterBag
 * @package Pingen\Support
 */
abstract class CollectionParameterBag extends ParameterBag
{
    /**
     * @param int $pageNumber
     * @return static
     */
    public function setPageNumber(int $pageNumber)
    {
        $page = $this->get('page', []);
        $page['number'] = $pageNumber;
        $this->set('page', $page);

        return $this;
    }

    /**
     * @param int $pageLimit
     * @return static
     */
    public function setPageLimit(int $pageLimit)
    {
        $page = $this->get('page', []);
        $page['limit'] = $pageLimit;
        $this->set('page', $page);

        return $this;
    }

    /**
     * @param string $sort
     * @return static
     */
    public function setSort(string $sort)
    {
        $this->set('sort', $sort);

        return $this;
    }

    /**
     * @param array $filter
     * @return static
     */
    public function setFilter(array $filter)
    {
        $this->set('filter', json_encode($filter));

        return $this;
    }

    /**
     * @param string $q
     * @return static
     */
    public function setQ(string $q)
    {
        $this->set('q', $q);

        return $this;
    }

    /**
     * @param array $include
     * @return static
     */
    public function setInclude(array $include)
    {
        $this->set('include', collect($include)->join(','));

        return $this;
    }
}

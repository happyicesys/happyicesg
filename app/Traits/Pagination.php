<?php

namespace App\Traits;

/**
 * Trait Pagination
 * @package App\Http\Traits
 */
trait Pagination
{
    /**
     * @var int
     */
    private $pageSizeLimit = 500;

    /**
     * @return bool|mixed
     */
    public function getPerPage()
    {
        $pageSize = request('pageNum');
        if (strtolower($pageSize) == 'all') {
            return false;
        }
        return min($pageSize, $this->pageSizeLimit);
    }

    /**
     * @return bool
     */
    public function isWithoutPagination()
    {
        $pageSize = request('pageNum');
        if (strtolower($pageSize) == 'all') {
            return true;
        }
        return false;
    }
}

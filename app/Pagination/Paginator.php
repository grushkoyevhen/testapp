<?php

namespace App\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{
    public $urlResolver;

    public function url($page)
    {
        return !$this->urlResolver
            ? parent::url($page)
            : call_user_func($this->urlResolver, $page);
    }
}

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

    protected function elements()
    {
        $window = UrlWindow::make($this);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }
}

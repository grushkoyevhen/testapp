<?php

namespace App\Pagination;

use Illuminate\Pagination\UrlWindow as UrlWindowGeneric;

class UrlWindow extends UrlWindowGeneric
{
    public function getStart()
    {
        return $this->paginator->getUrlRange(1, 1);
    }

    public function getFinish()
    {
        return $this->paginator->getUrlRange(
            $this->lastPage(),
            $this->lastPage()
        );
    }
}

<?php

namespace App\Model;

class LocationEntry
{
    public $location;
    public $products = [];

    /**
     * @param string $location
     * @param array $products
     */
    public function __construct(string $location = '', array $products = [])
    {
        $this->location = $location;
        $this->products = $products;
    }

    public function __toArray()
    {
        return call_user_func('get_object_vars', $this);
    }
}
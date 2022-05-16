<?php

namespace App\Provider\Product;

interface ProductProviderInterface
{
    public function filterProducts(?array $products, ?array $filterOptions): array;
}
<?php

namespace App\Controller;

use App\Provider\Product\ProductProviderInterface;
use App\Util\Error;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Files Controller
 * @Route("/products", name="products_")
 */
class ProductController extends AbstractFOSRestController
{
    private $fileController;
    private $productProvider;

    public function __construct(
        FileController           $controller,
        ProductProviderInterface $productProvider
    )
    {
        $this->fileController = $controller;
        $this->productProvider = new $productProvider();
    }

    /**
     * @Rest\Post("/filter")
     * @param Request $request
     * @return Response
     */
    public function filter(Request $request): Response
    {
        try {
            $products = $this->fileController->readFileData()->getContent();
            $products = (array)json_decode($products);
            $filters = $request->toArray()['filters'] ?? [];

            if (array_key_exists('code', $products)) {
                return new Response(
                    json_encode($products),
                    Response::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/json']
                );
            }

            $content = $this->productProvider->filterProducts($products, $filters);
            return new Response(
                json_encode($content),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } catch (Exception $e) {
            return new Response(
                Error::jsonError($e),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json']
            );
        }
    }
}
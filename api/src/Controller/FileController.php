<?php

namespace App\Controller;

use App\Provider\File\FileProviderInterface;
use App\Util\Error;
use App\Util\FileValidations;
use App\Util\RedisClient;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Files Controller
 * @Route("/files", name="files_")
 */
class FileController extends AbstractFOSRestController
{
    private $redisClient;
    private $fileProvider;
    private $baseDir = __DIR__ . '/../../uploads';

    /**
     * @throws Exception
     */
    public function __construct(
        RedisClient           $client,
        FileProviderInterface $fileProvider
    )
    {
        $this->redisClient = $client::getAdapter();
        $this->fileProvider = new $fileProvider();
    }

    /**
     * @Rest\Post("/upload")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function uploadFile(Request $request): Response
    {
        try {
            $body = $request->toArray();
            if (empty($body)) {
                throw Error::message(Error::EMPTY_PAYLOAD);
            }

            if (!array_key_exists('file', $body) || !array_key_exists('fileName', $body)) {
                throw Error::message(Error::INVALID_PAYLOAD, [
                    '@PAYLOAD@' => '{file: [], fileName: String}'
                ]);
            }

            $file = $body['file'];
            $fileName = $body['fileName'];
            if (!is_array($file) || !is_string($fileName) ||
                empty($file) || empty($fileName)
            ) {
                throw Error::message(Error::INVALID_PAYLOAD, [
                    '@PAYLOAD@' => '{file: [], fileName: String}'
                ]);
            }

            if ($this->redisClient->get('original_fileName') == $fileName) {
                throw Error::message(Error::FILE_ALREADY_UPLOADED);
            }

            $result = $this->fileProvider->uploadFile($file, $fileName);
            if ($result) {
                $this->redisClient->set('original_fileName', $fileName);
            }

            return $this->readFileData();
        } catch (Exception $e) {
            if ($e->getCode() == 0) {
                $exception = Error::message(Error::EMPTY_PAYLOAD);
                return new Response(
                    Error::jsonError($exception),
                    Response::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/json']
                );
            }

            return new Response(
                Error::jsonError($e),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json']
            );
        }
    }

    /**
     * @Rest\Get("/read")
     * @Rest\Head("/read")
     * @return Response
     * @throws Exception
     */
    public function readFileData(): Response
    {
        try {
//            $this->redisClient->unlink('actual_file');
            $file = FileValidations::getFileFromUpload();
            if ($this->redisClient->get('actual_file') != $file) {
                $products = $this->fileProvider->readFile("$this->baseDir/$file");

                $this->redisClient->set('actual_file', $file);
                $this->redisClient->set('products', json_encode($products));
            }

            $content = $this->redisClient->get('products');
            return new Response(
                $content,
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
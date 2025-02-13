<?php

namespace App\Http\Traits\Helpers;

use App\Http\Resources\EmptyResource;
use App\Http\Resources\EmptyResourceCollection;
use App\Http\Resources\AbstractJsonCollection;
use App\Http\Resources\AbstractJsonResource;
use Exception;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function okWithResource(AbstractJsonResource $resource, $headers = [], $customMessage = null)
    {
        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource
        $message = $customMessage != null ? $customMessage : ($resource->getModelName() != null ? $resource->getModelName() . " returned sucesfully." : "Record returned sucesfully.");
        return $this->apiResponse(
            [
                'result' => $resource,
                'message' => $message
            ],
            200,
            $headers
        );
    }

    protected function okNoRecords($message = 'No records found.', $headers = [])
    {
        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource

        return $this->apiResponse(
            [
                //'result' => $resource,
                'message' => $message
            ],
            200,
            $headers
        );
    }

    /**
     * Respond with not found.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function notFound($message = 'Resource not found', Exception $exception = null)
    {
        return $this->respondError($message, $exception, 404);
    }
    protected function apiResponse($data = [], $statusCode = 200, $headers = [])
    {
        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource

        $result = $this->parseGivenData($data, $statusCode, $headers);


        return response()->json(
            $result['content'],
            $result['statusCode'],
            $result['headers']
        );
    }

    protected function okWithCollection(AbstractJsonCollection $resourceCollection, $headers = [])
    {
        $message = '';
        if ($resourceCollection->getModelName()) {
            $message = $resourceCollection->getModelName() . " list returned.";
        }
        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource
        return $this->apiResponse(
            [
                'result' => $resourceCollection->response()->getData(),
                'message' => $message
            ],
            200,
            $headers
        );
    }

    protected function created(AbstractJsonResource $resource, $headers = [])
    {
        $message = $resource->getModelName() != null ? $resource->getModelName() . " created succesfully." : "Record created succesfully.";
        return $this->apiResponse(
            [
                'result' => $resource,
                'message' => $message
            ],
            201,
            $headers
        );
    }

    protected function updated(AbstractJsonResource $resource, $headers = [])
    {
        $message = $resource->getModelName() != null ? $resource->getModelName() . " updated succesfully." : "Record updated succesfully.";
        return $this->apiResponse(
            [
                'result' => $resource,
                'message' => $message
            ],
            201,
            $headers
        );
    }

    protected function restored(EmptyResource $resource, $headers = [])
    {
        $message = "Resource restored succesfully.";
        return $this->apiResponse(
            [
                'result' => $resource,
                'message' => $message
            ],
            204,
            $headers
        );
    }

    protected function deleted(EmptyResource $resource, $headers = [])
    {
        $message = "Resource deleted succesfully.";
        return $this->apiResponse(
            [
                'result' => $resource,
                'message' => $message
            ],
            204,
            $headers
        );
    }

    protected function noContent($headers = [])
    {
        return $this->apiResponse(
            [
                'message' => 'No content',
            ],
            204,
            $headers
        );
    }

    protected function noContentResource($message = 'No content')
    {
        return $this->okWithResource(new EmptyResource([]), $message);
    }

    protected function noContentResourceCollection($message = 'No Content Found')
    {
        return $this->okWithCollection(new EmptyResourceCollection([]), $message);
    }
    protected function respondError($message = "Something went wrong!", Exception $exception = null, $statusCode = 500)
    {
        return $this->apiResponse(
            [
                'message' => $message,
                'exception' => $exception,
                'result' => new EmptyResource([]),
            ],
            $statusCode
        );
    }

    /**
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return array
     */
    public function parseGivenData($data = [], $statusCode = 200, $headers = [])
    {
        $responseStructure = [
            'message' => $data['message'] ?? null,
            'result' => $data['result'] ?? null,
        ];
        if (isset($data['errors'])) {
            $responseStructure['errors'] = $data['errors'];
        }
        if (isset($data['status'])) {
            $statusCode = $data['status'];
        }


        if (isset($data['exception']) && ($data['exception'] instanceof Error || $data['exception'] instanceof Exception)) {
            if (config('app.env') !== 'production') {
                $responseStructure['exception'] = [
                    'message' => $data['exception']->getMessage(),
                    'file' => $data['exception']->getFile(),
                    'line' => $data['exception']->getLine(),
                    'code' => $data['exception']->getCode(),
                    'trace' => $data['exception']->getTrace(),
                ];
            }

            if ($statusCode === 200) {
                $statusCode = 500;
            }
        }
        return ["content" => $responseStructure, "statusCode" => $statusCode, "headers" => $headers];
    }


}

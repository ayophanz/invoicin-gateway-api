<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Request;
use Response;

trait Restable
{
    /**
     * @var int The default status code.
     */
    protected $statusCode = 200;

    /**
     * Method used to paginate a set of items.
     *
     * @param LengthAwarePaginator $paginator The paginator.
     * @param string[] $data The data to be paginated
     * @param string[] $counters The quantity of the subMenu items
     * @param string[] $headers The headers to be send.
     *
     * @return JsonResponse The paginated results in a JSON response.
     */
    public function respondWithPagination(
        LengthAwarePaginator $paginator,
        array $data,
        array $counters = [],
        array $headers = []
    ): JsonResponse {
        $data = [
            'data' => $data,
            'pagination' => [
                'counters' => $counters,
                'last_page' => $paginator->lastPage(),
                'current_page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total_count' => $paginator->total(),
            ],
        ];

        return $this->respond($data, $headers);
    }

    /**
     * Method used to paginate a array.
     *
     * @param array $data The data array.
     * @param int $count The full data count before pagination.
     * @param array $headers The headers to be send.
     *
     * @return JsonResponse The paginated results in a JSON response.
     */
    public function respondWithPaginationFromCollection(
        array $data,
        int $count,
        array $headers = []
    ): JsonResponse {
        $page = Request::has('page') ? Request::input('page') : 1;
        $pagination = Request::has('limit') ? Request::input('limit') : 5;

        $paginator = new Paginator(
            $data,
            $count,
            $pagination
        );
        $data = [
            'data' => $data,
            'pagination' => [
                'total_count' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'current_page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
            ],
        ];

        return $this->respond($data, $headers);
    }

    /**
     * Method used to return a set of items.
     *
     * @param string[] $data The data to be paginated
     * @param string[] $headers The given headers.
     *
     * @return JsonResponse The results in a JSON response.
     */
    public function respondWithData(array $data, $headers = []): JsonResponse
    {
        $data = [
            'data' => $data,
        ];

        return $this->respond($data, $headers);
    }

    /**
     * Will return a JSON response.
     *
     * @param string[] $data The given data.
     * @param string[] $headers The given headers.
     *
     * @return JsonResponse The JSON-response.
     */
    public function respond(array $data, array $headers = []): JsonResponse
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Getter for the status code.
     *
     * @return int The current status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Setter for the status code.
     *
     * @param int $statusCode The new status code.
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Will result in a JSON response with a 201 code.
     *
     * @param string $message The given message.
     * @param array $headers The headers that should be send with the JSON-response.
     *
     * @return JsonResponse The JSON-response with the message.
     */
    protected function respondCreated($message = 'Item created', $headers = []): JsonResponse
    {
        $this->setStatusCode(IlluminateResponse::HTTP_CREATED);

        return $this->respondWithSuccess($message, $headers);
    }

    /**
     * Will result in a JSON-response with a success message.
     *
     * @param string $message The given success message.
     * @param string[] $headers The headers that should be send with the JSON-response.
     *
     * @return JsonResponse The JSON-response with the success message.
     */
    public function respondWithSuccess(string $message, array $headers = []): JsonResponse
    {
        return $this->respond(
            [
                'success' => [
                    'message' => $message,
                    'status_code' => $this->getStatusCode(),
                ],
            ],
            $headers
        );
    }

    /**
     * Will result in a 401 error code, wrapped inside a JSON response.
     *
     * @param string $message The given error message.
     * @param string[] $headers The headers that should be send with the JSON-response.
     *
     * @return JsonResponse The JSON-response with the error code.
     */
    protected function respondUnauthorized(
        string $message = 'Unauthorized',
        int $code = 40100,
        array $headers = []
    ): JsonResponse {
        $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED);

        return $this->respond(
            [
                'error' => [
                    'message' => $message,
                    'code' => $code,
                    'status_code' => $this->getStatusCode(),
                ],
            ],
            $headers
        );
    }

    /**
     * Will result in a JSON-response with an error message.
     *
     * @param string $message The given error message.
     * @param string[] $headers The headers that should be send with the JSON-response.
     *
     * @return JsonResponse The JSON-response with the error message.
     */
    public function respondWithError(
        string $message = 'Error',
        int $code,
        array $headers = []
    ): JsonResponse {

        $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR);

        return $this->respond(
            [
                'error' => [
                    'message' => $message,
                    'code' => $code,
                    'status_code' => $this->getStatusCode(),
                ],
            ],
            $headers
        );
    }
}

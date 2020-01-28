<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;

class ApiErrorException extends HttpResponseException
{
    /**
     * ApiErrorException constructor.
     * @param string $error
     * @param array|null $details
     * @param int $code
     */
    public function __construct(string $error, ?array $details = [], $code = 400)
    {
        if (empty($details)) {
            $details = null;
        }

        if (is_array($details)) {
            $details = array_map(function ($item) {
                return (array) $item;
            }, $details);
        }

        $body = [
            'errors' => [
                [
                    'title' => $error,
                    'status' => $code,
                ]
            ],
        ];

        if (!empty($details)) {
            $body['errors'][0]['detail'] = $details;
        }

        parent::__construct(response()->json($body, $code));
    }
}

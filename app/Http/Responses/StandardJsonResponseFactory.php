<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use JsonSerializable;

class StandardJsonResponseFactory
{
    public function createJsonResponse(JsonSerializable $data, array $links = [], array $meta = []): JsonResponse
    {
        $body = array_filter(
            [
                'data' => $data->jsonSerialize(),
                'meta' => $meta,
                'links' => $links
            ],
            fn (array $part) => !empty($part)
        );

        return new JsonResponse($body, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

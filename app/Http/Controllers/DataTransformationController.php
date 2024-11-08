<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransformDataRequest;
use App\Services\DataTransformerService;
use Illuminate\Http\JsonResponse;

class DataTransformationController extends Controller
{
    public function __construct(
        protected DataTransformerService $transformerService
    ) {
    }

    public function transform(TransformDataRequest $request): JsonResponse
    {
        try {
            $result = $this->transformerService->transform(
                $request->claim_id,
                $request->endpoint_id
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}

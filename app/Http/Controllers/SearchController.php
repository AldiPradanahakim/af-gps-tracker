<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => ['required', 'string', 'min:2'],
        ]);

        return response()->json(
            $this->searchService->search(
                $request->string('keyword')->toString(),
                $request->user()
            )
        );
    }
}

<?php

namespace Canvas\Http\Controllers;

use Canvas\Http\Requests\PortfolioCategoryRequest;
use Canvas\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

class PortfolioCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            Category::query()
                 ->select('id', 'name', 'created_at')
                 ->latest()
                 ->withCount('portfolios')
                 ->paginate(), 200
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        return response()->json(Category::make([
            'id' => Uuid::uuid4()->toString(),
        ]), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PortfolioCategoryRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function store(PortfolioCategoryRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        $portfolio_category = Category::query()->find($id);

        if (! $portfolio_category) {
            if ($portfolio_category = Category::onlyTrashed()->firstWhere('slug', $data['slug'])) {
                $portfolio_category->restore();

                return response()->json($portfolio_category->refresh(), 201);
            } else {
                $portfolio_category = new Category(['id' => $id]);
            }
        }

        $portfolio_category->fill($data);

        $portfolio_category->user_id = $portfolio_category->user_id ?? request()->user('canvas')->id;

        $portfolio_category->save();

        return response()->json($portfolio_category->refresh(), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $portfolio_category = Category::query()->find($id);

        return $portfolio_category ? response()->json($portfolio_category, 200) : response()->json(null, 404);
    }

    /**
     * Display the specified relationship.
     *
     * @param $id
     * @return JsonResponse
     */
    public function showPortfolios($id): JsonResponse
    {
        $portfolio_category = Category::query()->with('portfolios')->find($id);

        return $portfolio_category ? response()->json($portfolio_category->portfolios()->paginate(), 200) : response()->json(null, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function destroy($id)
    {
        $portfolio_category = Category::query()->findOrFail($id);

        $portfolio_category->delete();

        return response()->json(null, 204);
    }
}

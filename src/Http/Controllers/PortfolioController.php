<?php

namespace Canvas\Http\Controllers;

use Canvas\Http\Requests\PortfolioRequest;
use Canvas\Models\Portfolio;
use Canvas\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $portfolios = Portfolio::query()
                    ->select('id', 'title', 'summary', 'featured_image', 'published_at', 'created_at', 'updated_at')
                     ->when(request()->user('canvas')->isContributor || request()->query('scope', 'user') != 'all', function (Builder $query) {
                         return $query->where('user_id', request()->user('canvas')->id);
                     }, function (Builder $query) {
                         return $query;
                     })
                     ->when(request()->query('type', 'published') != 'draft', function (Builder $query) {
                         return $query->published();
                     }, function (Builder $query) {
                         return $query->draft();
                     })
                     ->latest()
                     ->withCount('views')
                     ->paginate();

        $draftCount = Portfolio::query()
                          ->when(request()->user('canvas')->isContributor || request()->query('scope', 'user') != 'all', function (Builder $query) {
                              return $query->where('user_id', request()->user('canvas')->id);
                          }, function (Builder $query) {
                              return $query;
                          })
                          ->draft()
                          ->count();

        $publishedCount = Portfolio::query()
                              ->when(request()->user('canvas')->isContributor || request()->query('scope', 'user') != 'all', function (Builder $query) {
                                  return $query->where('user_id', request()->user('canvas')->id);
                              }, function (Builder $query) {
                                  return $query;
                              })
                              ->published()
                              ->count();

        return response()->json([
            'portfolios' => $portfolios,
            'draftCount' => $draftCount,
            'publishedCount' => $publishedCount,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $uuid = Uuid::uuid4();

        return response()->json([
            'portfolio' => Portfolio::make([
                'id' => $uuid->toString(),
                'slug' => "portfolio-{$uuid->toString()}",
            ]),
            'portfolio_categories' => Category::get(['name', 'slug']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PortfolioRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function store(PortfolioRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        $portfolio = Portfolio::query()
                    ->when($request->user('canvas')->isContributor, function (Builder $query) {
                        return $query->where('user_id', request()->user('canvas')->id);
                    }, function (Builder $query) {
                        return $query;
                    })
                    ->with('portfolio_category')
                    ->find($id);

        if (! $portfolio) {
            $portfolio = new Portfolio(['id' => $id]);
        }

        $portfolio->fill($data);

        $portfolio->user_id = $portfolio->user_id ?? request()->user('canvas')->id;

        $portfolio->save();

        $portfolio_categories = Category::query()->get(['id', 'name', 'slug']);

        $portfolio_categoryToSync = collect($request->input('portfolio_category', []))->map(function ($item) use ($portfolio_categories) {
            $portfolio_category = $portfolio_categories->firstWhere('slug', $item['slug']);

            if (! $portfolio_category) {
                $portfolio_category = Category::create([
                    'id' => $id = Uuid::uuid4()->toString(),
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'user_id' => request()->user('canvas')->id,
                ]);
            }

            return (string) $portfolio_category->id;
        })->toArray();

        $portfolio->portfolio_category()->sync($portfolio_categoryToSync);

        return response()->json($portfolio->refresh(), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $portfolio = Portfolio::query()
                    ->when(request()->user('canvas')->isContributor, function (Builder $query) {
                        return $query->where('user_id', request()->user('canvas')->id);
                    }, function (Builder $query) {
                        return $query;
                    })
                    ->with('portfolio_category:name,slug')
                    ->find($id);

        if ($portfolio) {
            return response()->json([
                'portfolio' => $portfolio,
                'portfolio_categories' => Category::query()->get(['name', 'slug']),
            ]);
        } else {
            return response()->json(null, 404);
        }
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
        $portfolio = Portfolio::query()
                    ->when(request()->user('canvas')->isContributor, function (Builder $query) {
                        return $query->where('user_id', request()->user('canvas')->id);
                    }, function (Builder $query) {
                        return $query;
                    })
                    ->findOrFail($id);

        $portfolio->delete();

        return response()->json(null, 204);
    }
}

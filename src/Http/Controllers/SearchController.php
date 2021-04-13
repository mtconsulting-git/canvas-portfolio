<?php

namespace Canvas\Http\Controllers;

use Canvas\Models\Category;
use Canvas\Models\Portfolio;
use Canvas\Models\Post;
use Canvas\Models\Tag;
use Canvas\Models\Topic;
use Canvas\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SearchController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function showPosts(): JsonResponse
    {
        $posts = Post::query()
                     ->select('id', 'title')
                     ->when(request()->user('canvas')->isContributor, function (Builder $query) {
                         return $query->where('user_id', request()->user('canvas')->id);
                     }, function (Builder $query) {
                         return $query;
                     })
                     ->latest()
                     ->get();

        $posts->map(function ($post) {
            $post['name'] = $post->title['it'];
            $post['type'] = 'Post';
            $post['route'] = 'edit-post';

            return $post;
        });

        return response()->json(collect($posts)->toArray(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function showTags(): JsonResponse
    {
        $tags = Tag::query()
                   ->select('id', 'name')
                   ->latest()
                   ->get();

        $tags->map(function ($tag) {
            $tag['name'] = $tag->name['it'];
            $tag['type'] = 'Tag';
            $tag['route'] = 'edit-tag';

            return $tag;
        });

        return response()->json(collect($tags)->toArray(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function showTopics(): JsonResponse
    {
        $topics = Topic::query()
                       ->select('id', 'name')
                       ->latest()
                       ->get();

        $topics->map(function ($topic) {
            $topic['name'] = $topic->name['it'];
            $topic['type'] = 'Topic';
            $topic['route'] = 'edit-topic';

            return $topic;
        });

        return response()->json(collect($topics)->toArray(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function showPortfolios(): JsonResponse
    {
        $portfolios = Portfolio::query()
            ->select('id', 'title')
            ->when(request()->user('canvas')->isContributor, function (Builder $query) {
                return $query->where('user_id', request()->user('canvas')->id);
            }, function (Builder $query) {
                return $query;
            })
            ->latest()
            ->get();

        $portfolios->map(function ($portfolios) {
            $portfolios['name'] = $portfolios->title['it'];
            $portfolios['type'] = 'Portfolio';
            $portfolios['route'] = 'edit-portfolio';

            return $portfolios;
        });

        return response()->json(collect($portfolios)->toArray(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function showPortfolioCategories(): JsonResponse
    {
        $portfolio_categories = Category::query()
            ->select('id', 'name')
            ->latest()
            ->get();

        $portfolio_categories->map(function ($portfolio_categories) {
            $portfolio_categories['name'] = $portfolio_categories->name['it'];
            $portfolio_categories['type'] = 'Category';
            $portfolio_categories['route'] = 'edit-portfolio_category';

            return $portfolio_categories;
        });

        return response()->json(collect($portfolio_categories)->toArray(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function showUsers(): JsonResponse
    {
        $users = User::query()
                     ->select('id', 'name', 'email')
                     ->latest()
                     ->get();

        $users->map(function ($user) {
            $user['type'] = 'User';
            $user['route'] = 'edit-user';

            return $user;
        });

        return response()->json(collect($users)->toArray(), 200);
    }
}

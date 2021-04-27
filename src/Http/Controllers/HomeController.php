<?php

namespace Canvas\Http\Controllers;

use Canvas\Canvas;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('canvas::layout')->with([
            'jsVars' => [
                'languageCodes' => Canvas::availableLanguageCodes(),
                'maxUpload' => config('canvas.upload_filesize'),
                'path' => Canvas::basePath(),
                'roles' => Canvas::availableRoles(),
                'timezone' => config('app.timezone'),
                'translations' => Canvas::availableTranslations(request()->user('canvas')->locale),
                'unsplash' => config('canvas.unsplash.access_key'),
                'user' => request()->user('canvas'),
                'version' => Canvas::installedVersion(),
                'portfolioEnabled' => config('canvas.portfolio.enabled'),
                'blogEnabled' => config('canvas.blog.enabled'),
                'languages' => explode(',', config('canvas.languages')),
                'google_translate' => config('canvas.google_translate.access_key'),
            ],
        ]);
    }


}

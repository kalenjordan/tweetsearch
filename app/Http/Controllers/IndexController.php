<?php

namespace App\Http\Controllers;

use Algolia\AlgoliaSearch\SearchClient;
use App\Blog;
use App\User;
use App\Util;
use Illuminate\Http\Request;

class IndexController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function welcome(Request $request)
    {
        $user = $request->session()->get('user');

        return view('welcome', [
            'error'       => $request->input('error'),
            'success'     => $request->input('success'),
            'user'        => $user,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function blog(Request $request, $slug)
    {
        $user = $request->session()->get('user');

        $blog = (new Blog())->lookupWithFilter("Slug = '$slug'");
        if (! $blog) {
            abort(404);
        }

        return view('blog', [
            'error'   => $request->input('error'),
            'success' => $request->input('success'),
            'user'    => $user,
            'blog'    => $blog,
        ]);
    }
}
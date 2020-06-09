<?php
/** @var \App\User $user */
?>

@extends('_landing')

@section('title')
    <title>
        Tweet Search | {{ Util::appName() }}
    </title>
@endsection

@section('content')
    <div class="mt-10 mx-auto max-w-screen-xl px-4 sm:my-12 sm:px-6 md:my-16 lg:my-20 xl:my-28">
        <div class="text-center">
            <h2 class="text-4xl tracking-tight leading-10 font-extrabold text-gray-900 sm:text-5xl sm:leading-none md:text-6xl">
                Search your <br class='lg:hidden'/> <span class='text-indigo-600'>tweets</span>
            </h2>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                See a demo of the search functionality which searches
                <a class="font-bold" href="https://twitter.com/paulg">@paulg</a>'s tweets.
            </p>
            <div class="my-5 max-w-md mx-auto sm:flex sm:justify-center md:my-8">
                <div class="rounded-md shadow">
                    <a href="javascript://" @click="toggleSearch" v-shortkey="['/']" @shortkey="toggleSearch"
                        class="bg-indigo-600 hover:bg-indigo-500 focus:shadow-outline-indigo focus:outline-none  w-full flex items-center justify-center px-8 py-3 border border-transparent text-base leading-6 font-medium rounded-md text-white transition duration-150 ease-in-out md:py-4 md:text-lg md:px-10">
                        Demo Search
                    </a>
                </div>
                <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                    <a href="https://github.com/kalenjordan/tweetsearch"
                       class="text-indigo-600 bg-white hover:text-indigo-500 w-full flex items-center justify-center px-8 py-3 border border-transparent text-base leading-6 font-medium rounded-md focus:outline-none focus:shadow-outline-blue transition duration-150 ease-in-out md:py-4 md:text-lg md:px-10">
                        Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12 bg-white">
        <div class="max-w-screen-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                {{--<p class="text-base leading-6 text-indigo-600 font-semibold tracking-wide uppercase">How it works</p>--}}
                <h3 class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl sm:leading-10">
                    Here's how it works
                </h3>
            </div>

            <div class="mt-10">
                <ul class="md:grid md:grid-cols-2 md:col-gap-8 md:row-gap-10">
                    <li>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    @include('svg.icon-eye', ['classes' => 'h-6 w-6'])
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg leading-6 font-medium text-gray-900">
                                    1. Download your twitter data
                                </h5>
                                <p class="mt-2 text-base leading-6 text-gray-500">
                                    Go into your twitter account and download your data under
                                    Settings > Account > Your Twitter Data.

                                    <br/><br/>
                                    Put the tweet.js file into the storage/app directory. Open it up in nano or vim
                                    and make one small change - remove the beginning "window.YTD.tweet.part0 = " part so that it starts
                                    with the opening bracket [
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-10 md:mt-0">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    @include('svg.icon-eye', ['classes' => 'h-6 w-6'])
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg leading-6 font-medium text-gray-900">
                                    2. Setup a free <a class="font-bold" href="https://algolia.com">algolia</a> index
                                </h5>
                                <p class="mt-2 text-base leading-6 text-gray-500">
                                    Setup a free algolia index - it's free for up to 10k records.
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-10 md:mt-0">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    @include('svg.icon-eye', ['classes' => 'h-6 w-6'])
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg leading-6 font-medium text-gray-900">
                                    3. Setup .env file
                                </h5>
                                <p class="mt-2 text-base leading-6 text-gray-500">
                                    Add your TWITTER_USERNAME along with credentials for a Twitter app - Twitter requires
                                    you to have an app to use the API even when you're just querying public data.
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-10 md:mt-0">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    @include('svg.icon-eye', ['classes' => 'h-6 w-6'])
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg leading-6 font-medium text-gray-900">
                                    4. Index your tweets
                                </h5>
                                <p class="mt-2 text-base leading-6 text-gray-500">
                                    Run `php artisan algolia:index`
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

@endsection

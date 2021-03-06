<?php

/** @var \App\User $user */

?>

<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant:wght@300&display=swap" rel="stylesheet">
    @yield('title')
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/svg" href="/img/logo.svg"/>
</head>
<body style="font-family: 'Cormorant', serif; font-size: 100%;">
    <div id="app" class="bg-gray-50">
        <div class="bg-gray-100" v-if="showSearch">
            <div class="p-4 max-w-lg mx-auto text-center">
                <search-component class="inline-block"
                                  :app-id="'{{ Util::algoliaAppId()  }}'" :public-api-key="'{{ Util::algoliaPublicKeyFor(isset($user) ? $user : null) }}'"></search-component>
                <a href="javascript://" class="hidden"
                   @click="toggleSearch" v-shortkey="['esc']" @shortkey="showSearch = false" v-tooltip="'esc'"
                >Close</a>
            </div>
        </div>

        @if (isset($error))
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @include('svg.icon-warning', ['classes' => 'h-5 w-5 text-red-400'])
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm leading-5 font-medium text-red-800">
                            {{ $error }}
                        </h3>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($success))
            Need new success message jobby {{ $success }}
        @endif

        <div class="relative overflow-hidden">
            <div class="hidden sm:block sm:absolute sm:inset-y-0 sm:h-full sm:w-full">
                <div class="relative h-full max-w-screen-xl mx-auto">
                    @include('svg.dot-pattern')
                </div>
            </div>

            <section id="nav" class="relative pt-6">
                <div class="max-w-screen-xl mx-auto px-4 sm:px-6" :class="{ 'opacity-0' : focusMode }">
                    <nav class="relative flex items-center justify-between sm:h-10 md:justify-center">
                        <div class="flex items-center flex-1 md:absolute md:inset-y-0 md:left-0">
                            <div class="flex items-center justify-between w-full md:w-auto">
                                <a href="/" v-shortkey="['h']" @shortkey="clickLink($event)">
                                    @include('svg.logo', ['classes' => 'h-8 w-auto sm:h-10 text-indigo-500 hover:scale-105 transform duration-150'])
                                </a>
                                <div class="-mr-2 flex items-center md:hidden">
                                    <button @click="toggleMobileMenu" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                        @include('svg.menu', ['classes' => 'h-6 w-6'])
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{--<div class="hidden md:block">--}}
                            {{--<a href="javascript://" @click="toggleSearch" v-shortkey="['/']" @shortkey="toggleSearch"--}}
                               {{--class="font-medium text-gray-500 hover:text-gray-900 focus:outline-none focus:text-gray-900 transition duration-150 ease-in-out">--}}
                                {{--Search--}}
                            {{--</a>--}}
                            {{--<a href="https://github.com/kalenjordan/tweetsearch" class="ml-10 font-medium text-indigo-500 hover:text-indigo-900 focus:outline-none focus:text-indigo-900 transition duration-150 ease-in-out">--}}
                                {{--Download--}}
                            {{--</a>--}}
                        {{--</div>--}}
                        <div class="hidden z-10 md:absolute md:flex md:items-center md:justify-end md:inset-y-0 md:right-0">
                            @if (isset($user) && $user)
                                <span class="inline-flex">
                                    <div class="inline-block text-left">
                                        <div>
                                            <button @click="toggleAccountMenu" class="max-w-xs flex items-center text-sm rounded-full text-white focus:outline-none focus:shadow-solid" id="user-menu" aria-label="User menu" aria-haspopup="true">
                                                <img class="header-avatar h-8 w-8 rounded-full"
                                                     src="{{ $user->avatar() }}" alt=""/>
                                            </button>
                                        </div>

                                        <!--
                                          Dropdown panel, show/hide based on dropdown state.

                                          Entering: "transition ease-out duration-100"
                                            From: "transform opacity-0 scale-95"
                                            To: "transform opacity-100 scale-100"
                                          Leaving: "transition ease-in duration-75"
                                            From: "transform opacity-100 scale-100"
                                            To: "transform opacity-0 scale-95"
                                        -->
                                        <div v-if="showAccountMenu" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg">
                                            <div class="rounded-md bg-white shadow-xs">
                                                <div class="py-1">
                                                    <a href="/account/settings" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">Settings</a>
                                                    <a href="/logout" class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                                        Sign out
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </span>
                            @else
                                {{--<span class="inline-flex rounded-md shadow">--}}
                                    {{--<a href="/auth"--}}
                                       {{--v-shortkey="['l']" @shortkey="clickLink($event)" v-tooltip="'L key'"--}}
                                       {{--class="inline-flex items-center px-4 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-indigo-600 bg-white hover:text-indigo-500 focus:outline-none focus:shadow-outline-blue active:bg-gray-50 active:text-indigo-700 transition duration-150 ease-in-out">--}}
                                        {{--Log in--}}
                                    {{--</a>--}}
                                {{--</span>--}}
                            @endif
                        </div>
                    </nav>
                </div>

                <!--
                  Mobile menu, show/hide based on menu open state.

                  Entering: "duration-150 ease-out"
                    From: "opacity-0 scale-95"
                    To: "opacity-100 scale-100"
                  Leaving: "duration-100 ease-in"
                    From: "opacity-100 scale-100"
                    To: "opacity-0 scale-95"
                -->
                <div v-if="showMobileMenu" class="z-10 absolute top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden">
                    <div class="rounded-lg shadow-md">
                        <div class="rounded-lg bg-white shadow-xs overflow-hidden">
                            <div class="px-5 pt-4 flex items-center justify-between">
                                <div>
                                    <a href="/">
                                        @include('svg.logo', ['classes' => 'h-8 w-auto'])
                                    </a>
                                </div>
                                <div class="-mr-2">
                                    <button @click="toggleMobileMenu" type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                        @include('svg.icon-close', ['classes' => 'h-6 w-6'])
                                    </button>
                                </div>
                            </div>
                            <div class="px-2 pt-2 pb-3">
                                <a href="#link1" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">
                                    Link 1
                                </a>
                                <a href="#link2" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">
                                    Link 2
                                </a>
                                <a href="#cta" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-indigo-700 hover:text-indigo-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">
                                    Call to action
                                </a>
                            </div>

                            @if (isset($user) && $user)
                                <div class="pt-4 pb-3 border-t border-gray-100">
                                    <div class="flex items-center px-5">
                                        <div class="flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full"
                                                 src="{{ $user->avatar() }}" alt=""/>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-base font-medium leading-none text-gray-600">
                                                {{ $user->name() }}
                                            </div>
                                            <div class="mt-1 text-sm font-medium leading-none text-gray-400">
                                                {{ $user->email() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 px-2">
                                        <a href="/account/settings" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">
                                            Settings
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if (isset($user))
                                <div>
                                    <a href="/logout" class="block w-full px-5 py-3 text-center font-medium text-indigo-600 bg-gray-50 hover:bg-gray-100 hover:text-indigo-700 focus:outline-none focus:bg-gray-100 focus:text-indigo-700 transition duration-150 ease-in-out">
                                        Log out
                                    </a>
                                </div>
                            @else
                                {{--<div>--}}
                                    {{--<a href="/auth" class="block w-full px-5 py-3 text-center font-medium text-indigo-600 bg-gray-50 hover:bg-gray-100 hover:text-indigo-700 focus:outline-none focus:bg-gray-100 focus:text-indigo-700 transition duration-150 ease-in-out">--}}
                                        {{--Log in--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                            @endif
                        </div>
                    </div>
                </div>
                @yield('content')

            </section>
        </div>

        @include('footer')
    </div>

    @yield('scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>

<?php
/** @var \App\User $user */
?>

<div class="bg-gray-800" :class="{ 'opacity-0' : focusMode }">
    <div class="max-w-screen-xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
            <div class="xl:col-span-1 mb-8">
                <a href="/">
                    @include('svg.logo', ['classes' => 'h-8 w-auto sm:h-10 text-gray-500 hover:opacity-50 transition ease-in-out duration-150'])
                </a>

                <p class="mt-4 text-gray-400 text-xl leading-6">
                    Search your tweets.
                </p>
            </div>

        </div>
        <div class="mt-8 border-t border-gray-700 pt-8 md:flex md:items-center md:justify-between">
            <div class="flex md:order-2">
                <a href="https://twitter.com/kalenjordan" class="ml-6 text-gray-400 hover:text-gray-300">
                    <span class="sr-only">Twitter</span>
                    @include('svg.icon-twitter', ['classes' => 'h-6 w-6'])
                </a>
            </div>
            <p class="mt-8 text-base leading-6 text-gray-400 md:mt-0 md:order-1">
                &copy; 2020. All rights reserved.
            </p>
        </div>
    </div>
</div>

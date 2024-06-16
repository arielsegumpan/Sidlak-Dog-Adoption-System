
@props(['post'])
<a class="group rounded-xl overflow-hidden" href="#!">
    <div class="sm:flex">
    <div class="flex-shrink-0 relative rounded-xl overflow-hidden w-full sm:w-56 h-44">
        <img class="group-hover:scale-105 transition-transform duration-500 ease-in-out size-full absolute top-0 start-0 object-cover rounded-xl" src="{{ asset(Storage::url($post->post_image)) }}" alt="{{ $post->post_title }}">
    </div>

    <div class="grow mt-4 sm:mt-0 sm:ms-6 px-4 sm:px-0">
        <h3 class="text-xl font-semibold text-gray-800 group-hover:text-gray-600 dark:text-neutral-300 dark:group-hover:text-white">
            {{ $post->post_title }}
        </h3>
        <p class="mt-3 text-gray-600 dark:text-neutral-400">
            {!! truncate_html($post->post_content, 100) !!}
        </p>
        <p class="mt-4 inline-flex items-center gap-x-1 text-blue-600 decoration-2 hover:underline font-medium">
        Read more
        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </p>
    </div>
    </div>
</a>

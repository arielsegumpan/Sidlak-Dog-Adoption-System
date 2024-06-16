@props(['posts'])

@foreach ($this->posts as $post)
 <!-- Card -->
 <a class="group sm:flex rounded-xl" href="#">
    <div class="flex-shrink-0 relative rounded-xl overflow-hidden h-[200px] sm:w-[250px] sm:h-[350px] w-full">
      <img class="size-full absolute top-0 start-0 object-cover" src="{{ asset(Storage::url($post->post_image)) }}" alt="{{ $post->post_title }}">
    </div>

    <div class="grow">
      <div class="p-4 flex flex-col h-full sm:p-6">
        <div class="mb-3">
            @foreach ($post->categories as $category )
                <p class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-neutral-800 dark:text-neutral-200">
                {{ $category->category_name }}
                </p>
            @endforeach
        </div>
        <h3 class="text-lg sm:text-2xl font-semibold text-gray-800 group-hover:text-blue-600 dark:text-neutral-300 dark:group-hover:text-white">
            {{ $post->post_title }}
        </h3>
        <p class="mt-2 text-gray-600 dark:text-neutral-400">
            {{ truncate_html($post->post_content, 100) }}
        </p>

        <div class="mt-5 sm:mt-auto">
          <!-- Avatar -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <img class="size-[46px] rounded-full" src="{{ $post->author->profile_photo_path ? asset(Storage::url($post->author->profile_photo_path)) : $post->author->profile_photo_url }}" alt="{{ $post->author->name }}">
            </div>
            <div class="ms-2.5 sm:ms-4">
              <h4 class="font-semibold text-gray-800 dark:text-neutral-200">
                {{ $post->author->name }}
              </h4>
              <p class="text-xs text-gray-500 dark:text-neutral-500">
                {{ $post->created_at->diffForHumans() }}
              </p>
            </div>
          </div>
          <!-- End Avatar -->
        </div>
      </div>
    </div>
  </a>
  <!-- End Card -->
  @endforeach

 @props(['dogs'])
 <!-- Sidebar -->
 <aside class="w-full md:w-1/4 p-4 bg-white dark:bg-neutral-800 rounded mb-4 md:mb-0">
    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-neutral-300">{{ __('Breeds') }}</h2>
    <ul>
        @foreach ($this->breeds as $breed)
        <li><a href="#" class="block p-2 text-gray-700 dark:text-neutral-400 hover:bg-gray-200 dark:hover:bg-neutral-700 rounded">{{ $breed->breed_name }}</a></li>
        @endforeach
    </ul>
</aside>
<!-- Main Content -->
<div class="w-full md:w-3/4 p-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($this->dogs as $dog )
        <div class="bg-gray-100 text-gray-800 dark:bg-neutral-800 dark:text-neutral-200 p-4 rounded shadow-md group">
            <img src="{{ asset(Storage::url($dog->first_image)) }}" alt="{{ $dog->dog_name }}" class="w-full h-48 object-cover rounded">
            <h3 class="mt-4 text-lg font-semibold text-gray-800 group-hover:text-blue-600 dark:text-neutral-300 dark:group-hover:text-white">{{ $dog->dog_name }}</h3>
            <p class="text-gray-600 dark:text-neutral-400">({{ $dog->breed->breed_name }})</p>
            <p class="text-gray-600 dark:text-neutral-400"><Strong>{{ __('Age: ') }}</Strong>{{ $dog->dog_age }}</p>


        </div>
        @endforeach

    </div>
    <div class="flex flex-wrap justify-items-between items-center">
        {{ $this->dogs->links() }}
    </div>
</div>

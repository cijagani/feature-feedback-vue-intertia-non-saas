<div class="jets-search">
    @foreach($headings as $h1)
        <div class="mb-3">
            <h3 class="text-xs font-semibold text-gray-900 dark:text-white mb-2 uppercase tracking-wider">
                <a href="#{{ $h1['slug'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                    {{ $h1['name'] }}
                </a>
            </h3>

            @if(count($h1['subheadings']) > 0)
                @foreach($h1['subheadings'] as $h2)
                    <a href="#{{ $h2['slug'] }}"
                       class="flex items-center px-2 py-1 text-xs rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500 mr-2 flex-shrink-0"></span>
                        <span class="text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white font-medium truncate">
                            {{ $h2['name'] }}
                        </span>
                    </a>

                    @if(count($h2['subheadings']) > 0)
                        @foreach($h2['subheadings'] as $h3)
                            <a href="#{{ $h3['slug'] }}"
                               class="flex items-center px-4 py-0.5 text-xs rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors ml-1">
                                <span class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white truncate">
                                    {{ $h3['name'] }}
                                </span>
                            </a>
                        @endforeach
                    @endif
                @endforeach
            @endif
        </div>
    @endforeach
</div>

@php
    use Knuckles\Scribe\Tools\Utils as u;
    /** @var  Knuckles\Camel\Output\OutputEndpointData $endpoint */
    $method = strtoupper($endpoint->httpMethods[0]);
    $methodColors = [
        'GET' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-700',
        'POST' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-700',
        'PUT' => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-700',
        'PATCH' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-700',
        'DELETE' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-700',
    ];
    $methodColor = $methodColors[$method] ?? 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-700';
@endphp

<div id="{{ $endpoint->fullSlug() }}" class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Endpoint Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-1 rounded-md text-xs font-semibold border {{ $methodColor }}">
                        {{ $method }}
                    </span>
                    @if($endpoint->isAuthed())
                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-700">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Auth Required
                        </span>
                    @endif
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $endpoint->name() }}</h3>
                @if($endpoint->metadata->description)
                    <div class="prose dark:prose-invert max-w-none text-sm">
                        {!! Parsedown::instance()->text($endpoint->metadata->description) !!}
                    </div>
                @endif
            </div>
        </div>

        <!-- Endpoint URL -->
        <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3">
            <div class="flex items-center">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mr-2">{{ $method }}</span>
                <code class="flex-1 text-sm text-gray-900 dark:text-white font-mono bg-transparent">{{ $endpoint->uri }}</code>
                <button onclick="copyToClipboard('{{ $endpoint->uri }}')" class="ml-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="p-4">
        <!-- Parameters -->
        @if(count($endpoint->urlParameters) || count($endpoint->queryParameters) || count($endpoint->bodyParameters))
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Parameters
                </h4>

                @if(count($endpoint->urlParameters))
                    @include('scribe::themes.tailwind.parameters', ['parameters' => $endpoint->urlParameters, 'title' => 'URL Parameters'])
                @endif

                @if(count($endpoint->queryParameters))
                    @include('scribe::themes.tailwind.parameters', ['parameters' => $endpoint->queryParameters, 'title' => 'Query Parameters'])
                @endif

                @if(count($endpoint->bodyParameters))
                    @include('scribe::themes.tailwind.parameters', ['parameters' => $endpoint->bodyParameters, 'title' => 'Body Parameters'])
                @endif
            </div>
        @endif

        <!-- Code Examples -->
        @isset($metadata['example_languages'])
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Example Request
                </h4>

                <!-- Language Tabs -->
                <div class="flex space-x-1 mb-3">
                    @foreach($metadata['example_languages'] as $lang)
                        <button class="language-tab px-3 py-1.5 text-sm rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 {{ $loop->first ? 'active' : '' }}"
                                data-language="{{ $lang }}"
                                onclick="showLanguage('{{ $lang }}')">
                            {{ ucfirst($lang) }}
                        </button>
                    @endforeach
                </div>

                @foreach($metadata['example_languages'] as $language)
                    <div class="{{ $language }}-example" style="{{ $loop->first ? 'display: block;' : 'display: none;' }}">
                        <div class="relative bg-gray-900 rounded-lg overflow-hidden">
                            <!-- Terminal header -->
                            <div class="flex items-center px-4 py-2 bg-gray-800">
                                <div class="flex space-x-2">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="flex-1 text-center">
                                    <span class="text-xs text-gray-400 font-mono">{{ ucfirst($language) }}</span>
                                </div>
                                <button onclick="copyCodeBlock(this)" class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            <pre class="p-4 text-sm text-gray-100 overflow-x-auto"><code class="language-{{ $language }}">@include("scribe::partials.example-requests.$language")</code></pre>
                        </div>
                    </div>
                @endforeach
            </div>
        @endisset

        <!-- Response Examples -->
        @if($endpoint->isGet() || $endpoint->hasResponses())
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Example Response
                </h4>

                @foreach($endpoint->responses as $response)
                    <div class="mb-3">
                        <div class="flex items-center mb-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($response->status >= 200 && $response->status < 300) bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-700
                                @elseif($response->status >= 400 && $response->status < 500) bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-700
                                @else bg-yellow-100 text-yellow-800 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-700
                                @endif">
                                {{ $response->status }}
                            </span>
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $response->fullDescription() }}</span>
                        </div>

                        <div class="relative bg-gray-900 rounded-lg overflow-hidden">
                            <div class="flex items-center px-3 py-1.5 bg-gray-800">
                                <span class="text-xs text-gray-400 font-mono">Response</span>
                                <button onclick="copyCodeBlock(this)" class="ml-auto text-gray-400 hover:text-white transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            @if(is_string($response->content) && Str::startsWith($response->content, "<<binary>>"))
                                <pre class="p-3 text-sm text-gray-100 overflow-x-auto"><code>{{ u::trans("scribe::endpoint.responses.binary") }} - {{ str_replace("<<binary>>", "", $response->content) }}</code></pre>
                            @elseif($response->status == 204)
                                <pre class="p-3 text-sm text-gray-100 overflow-x-auto"><code>{{ u::trans("scribe::endpoint.responses.empty") }}</code></pre>
                            @else
                                @php($parsed = json_decode($response->content))
                                <pre class="p-3 text-sm text-gray-100 overflow-x-auto"><code class="language-json">{{ $parsed != null ? json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $response->content }}</code></pre>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Try it Out -->
        @if($tryItOut['enabled'] ?? true)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-2-8V6a2 2 0 012-2h4a2 2 0 012 2v2M3 20h18a1 1 0 001-1V9a1 1 0 00-1-1H3a1 1 0 00-1 1v10a1 1 0 001 1z"></path>
                    </svg>
                    Test this endpoint
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">You can test this API endpoint right here. Make sure you have proper authentication if required.</p>

                <!-- Try it out form will be injected here by Scribe -->
                <div id="try-it-out-{{ $endpoint->endpointId() }}"></div>
            </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity';
        toast.textContent = 'Copied to clipboard!';
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 2000);
    });
}
</script>

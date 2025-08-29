<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ $metadata['title'] }}</title>
    <meta name="description" content="{{ $metadata['description'] ?? '' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'mono': ['JetBrains Mono', 'Monaco', 'Consolas', 'monospace'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        whatsapp: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-10px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                    }
                }
            }
        }
    </script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/outline/style.css">

    <!-- Highlight.js for code syntax highlighting -->
    <link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/github-dark.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <!-- Search functionality -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .whatsapp-gradient {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        }

        .code-block {
            background: #1e1e1e;
            border-radius: 0.75rem;
            position: relative;
        }

        .code-block::before {
            content: '';
            position: absolute;
            top: 1rem;
            left: 1rem;
            width: 12px;
            height: 12px;
            background: #ff5f56;
            border-radius: 50%;
            box-shadow: 20px 0 #ffbd2e, 40px 0 #27ca3f;
        }

        .prose pre {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.8);
        }

        /* Language tab styles */
        .language-tab {
            transition: all 0.2s ease;
        }

        .language-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Try it out form styles */
        .try-it-form input[type="text"],
        .try-it-form input[type="email"],
        .try-it-form input[type="password"],
        .try-it-form textarea,
        .try-it-form select {
            @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors;
        }

        .try-it-form button[type="submit"] {
            @apply bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 transform hover:scale-105;
        }

        /* Hide all language examples by default */
        .bash-example code { display: none; }
        .javascript-example code { display: none; }
        .php-example code { display: none; }
        .python-example code { display: none; }
    </style>

    <script>
        var tryItOutBaseUrl = "{!! $tryItOut['base_url'] ?? $baseUrl !!}";
        var useCsrf = Boolean({!! $tryItOut['use_csrf'] ?? null !!});
        var csrfUrl = "{!! $tryItOut['csrf_url'] ?? null !!}";
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans" data-languages='["bash", "javascript", "php", "python"]'>
    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="mobile-menu-button" class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <!-- Sidebar Header -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                @if($metadata['logo'] ?? false)
                    <img src="{{ $metadata['logo'] }}" alt="Logo" class="w-8 h-8 rounded-lg">
                @else
                    <div class="w-8 h-8 whatsapp-gradient rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.690"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $metadata['title'] }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">API Documentation</p>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="relative">
                <input type="text" id="input-search" placeholder="Search endpoints..."
                       class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll p-4" id="toc">
            <div class="jets-search">
                @foreach($headings as $h1)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">
                            <a href="#{{ $h1['slug'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                {{ $h1['name'] }}
                            </a>
                        </h3>

                        @if(count($h1['subheadings']) > 0)
                            @foreach($h1['subheadings'] as $h2)
                                <a href="#{{ $h2['slug'] }}"
                                   class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                                    <span class="w-2 h-2 rounded-full bg-primary-500 mr-3 flex-shrink-0"></span>
                                    <span class="text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white font-medium truncate">
                                        {{ $h2['name'] }}
                                    </span>
                                </a>

                                @if(count($h2['subheadings']) > 0)
                                    @foreach($h2['subheadings'] as $h3)
                                        <a href="#{{ $h3['slug'] }}"
                                           class="flex items-center px-6 py-1 text-xs rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors ml-3">
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
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                Generated with ❤️ by
                <a href="https://scribe.knuckles.wtf" target="_blank" class="text-primary-600 hover:text-primary-700">Scribe</a>
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 text-center mt-1">
                Last updated: {{ $metadata['last_updated'] ?? date('F j, Y') }}
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-80">
        <!-- Top Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metadata['title'] }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $metadata['description'] ?? '' }}</p>
                    </div>

                    <!-- Dark mode toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- API Usage Guide Section -->
        <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="px-6 py-8">
                <div class="max-w-6xl mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Getting Started with WhatsApp Marketing API</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                            Follow these simple steps to integrate our powerful WhatsApp marketing tools into your application.
                        </p>
                    </div>

                    <!-- Steps -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Step 1: Authentication -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Authenticate</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Get your API access token by logging in with your credentials.</p>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <code class="text-xs text-blue-600 dark:text-blue-400 font-mono">POST /api/auth/login</code>
                            </div>
                        </div>

                        <!-- Step 2: Set Headers -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Set Headers</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Include the Bearer token in all your API requests.</p>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <code class="text-xs text-green-600 dark:text-green-400 font-mono">Authorization: Bearer YOUR_TOKEN</code>
                            </div>
                        </div>

                        <!-- Step 3: Use Subdomain -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-10 h-10 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Use Subdomain</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Replace {subdomain} with your actual tenant subdomain in URLs.</p>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <code class="text-xs text-purple-600 dark:text-purple-400 font-mono">/api/v1/{your-subdomain}/</code>
                            </div>
                        </div>

                        <!-- Step 4: Make Requests -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-10 h-10 bg-orange-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Make Requests</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Start making API calls to manage contacts, campaigns, and more.</p>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <code class="text-xs text-orange-600 dark:text-orange-400 font-mono">GET /contacts</code>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Example -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                            Quick Example
                        </h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">1. Login to get token:</h4>
                                <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                    <pre class="text-sm text-gray-100"><code>curl -X POST https://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'</code></pre>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">2. Use token to fetch contacts:</h4>
                                <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                    <pre class="text-sm text-gray-100"><code>curl -X GET https://your-domain.com/api/v1/your-subdomain/contacts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="mt-6 p-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Important Information</h4>
                                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                                    <li>• <strong>Base URL:</strong> https://your-domain.com/api/v1/{subdomain}/</li>
                                    <li>• <strong>Authentication:</strong> Bearer token required for all protected endpoints</li>
                                    <li>• <strong>Rate Limiting:</strong> 60 requests per minute per API key</li>
                                    <li>• <strong>Response Format:</strong> JSON with consistent structure</li>
                                    <li>• <strong>Error Handling:</strong> Standard HTTP status codes with detailed error messages</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="min-h-screen">
            <div id="content" class="max-w-none prose prose-lg dark:prose-invert px-6 py-8">
                {!! $intro !!}

                {!! $auth !!}

                @include("scribe::themes.tailwind.groups")

                {!! $append !!}
            </div>
        </main>
    </div>

    <!-- Backdrop for mobile menu -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

    <!-- Scripts -->
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.2.1.js") }}"></script>

    <script>
        // Dark mode toggle
        const themeToggle = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');

        function setTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
                localStorage.setItem('theme', 'light');
            }
        }

        // Initialize theme
        const savedTheme = localStorage.getItem('theme') ||
                          (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        setTheme(savedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        function toggleMobileMenu() {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        mobileMenuButton.addEventListener('click', toggleMobileMenu);
        backdrop.addEventListener('click', toggleMobileMenu);

        // Initialize syntax highlighting
        hljs.highlightAll();

        // Language switching functionality
        const languages = ["bash", "javascript", "php", "python"];
        let currentLanguage = languages[0] || 'bash';

        function showLanguage(language) {
            // Hide all language examples
            languages.forEach(lang => {
                document.querySelectorAll(`.${lang}-example code`).forEach(el => {
                    el.style.display = 'none';
                });
                document.querySelectorAll(`.${lang}-example`).forEach(el => {
                    el.style.display = 'none';
                });
            });

            // Show selected language examples
            document.querySelectorAll(`.${language}-example code`).forEach(el => {
                el.style.display = 'block';
            });
            document.querySelectorAll(`.${language}-example`).forEach(el => {
                el.style.display = 'block';
            });

            // Update active tab
            document.querySelectorAll('.language-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll(`.language-tab[data-language="${language}"]`).forEach(tab => {
                tab.classList.add('active');
            });

            currentLanguage = language;
            localStorage.setItem('preferred-language', language);
        }

        // Initialize language from localStorage or default
        const savedLanguage = localStorage.getItem('preferred-language') || languages[0];
        if (languages.includes(savedLanguage)) {
            showLanguage(savedLanguage);
        }

        // Add language tabs to code blocks
        document.addEventListener('DOMContentLoaded', function() {
            const codeBlocks = document.querySelectorAll('pre code');

            codeBlocks.forEach(block => {
                const pre = block.parentElement;
                if (!pre.querySelector('.language-tabs')) {
                    const tabs = document.createElement('div');
                    tabs.className = 'language-tabs flex space-x-1 mb-4';

                    languages.forEach(lang => {
                        const tab = document.createElement('button');
                        tab.className = `language-tab px-3 py-1 text-sm rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors ${lang === currentLanguage ? 'active' : ''}`;
                        tab.textContent = lang.charAt(0).toUpperCase() + lang.slice(1);
                        tab.setAttribute('data-language', lang);
                        tab.addEventListener('click', () => showLanguage(lang));
                        tabs.appendChild(tab);
                    });

                    pre.parentElement.insertBefore(tabs, pre);
                }
            });

            // Initialize search functionality
            const searchInput = document.getElementById('input-search');
            if (searchInput && typeof Jets !== 'undefined') {
                new Jets({
                    searchTag: '#input-search',
                    contentTag: '#toc'
                });
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });

        // Add animation classes to elements as they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        });

        document.querySelectorAll('h1, h2, h3, .code-block').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>

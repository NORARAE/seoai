<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEOAI - SEO Location Intelligence Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">SEOAI</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/admin/login" class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                        Sign In
                    </a>
                    <a href="/dashboard" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors shadow-sm">
                        Open Dashboard →
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-full text-sm font-medium text-blue-700 mb-6">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                        </svg>
                        Powered by Laravel 12
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Location SEO Intelligence, 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">
                            Automated
                        </span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Generate, optimize, and manage thousands of location-specific SEO pages with AI-powered content, 
                        structured data, and intelligent internal linking.
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="/dashboard" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Launch Dashboard
                        </a>
                        <a href="#features" class="px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-xl font-semibold text-lg transition-colors">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border-2 border-gray-200 shadow-2xl">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                <span class="text-sm font-medium text-gray-700">System Health</span>
                                <span class="text-2xl font-bold text-green-600">92%</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                <span class="text-sm font-medium text-gray-700">Location Pages</span>
                                <span class="text-2xl font-bold text-blue-600">1,247</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                <span class="text-sm font-medium text-gray-700">Avg SEO Score</span>
                                <span class="text-2xl font-bold text-indigo-600">87.3</span>
                            </div>
                            <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border-2 border-green-200">
                                <div class="flex items-center gap-2 text-green-800 font-semibold text-sm">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    All systems operational
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Complete SEO Automation Platform</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to dominate local search at scale
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div class="bg-white rounded-xl p-8 border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Page Generation</h3>
                    <p class="text-gray-600">
                        Automatically generate thousands of optimized location pages with unique content, meta tags, and structured data.
                    </p>
                </div>

                <!-- Feature Card 2 -->
                <div class="bg-white rounded-xl p-8 border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Quality Scoring</h3>
                    <p class="text-gray-600">
                        Real-time SEO quality scores based on content completeness, meta optimization, and technical factors.
                    </p>
                </div>

                <!-- Feature Card 3 -->
                <div class="bg-white rounded-xl p-8 border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Internal Linking</h3>
                    <p class="text-gray-600">
                        Intelligent internal link suggestions based on location proximity, hierarchy, and topical relevance.
                    </p>
                </div>

                <!-- Feature Card 4 -->
                <div class="bg-white rounded-xl p-8 border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Schema Markup</h3>
                    <p class="text-gray-600">
                        Automatic generation of LocalBusiness, Service, and BreadcrumbList schemas for rich search results.
                    </p>
                </div>

                <!-- Feature Card 5 -->
                <div class="bg-white rounded-xl p-8 border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Performance Tracking</h3>
                    <p class="text-gray-600">
                        Monitor system health, page quality, and content completeness with actionable insights.
                    </p>
                </div>

                <!-- Feature Card 6 -->
                <div class="bg-white rounded-xl p-8 border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Batch Operations</h3>
                    <p class="text-gray-600">
                        Bulk render, validation, and export operations with queue management and progress tracking.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-blue-600 to-indigo-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Ready to scale your local SEO?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Start managing your location pages with intelligent automation today.
            </p>
            <a href="/dashboard" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-xl font-semibold text-lg hover:shadow-2xl transition-all transform hover:-translate-y-1">
                Open Dashboard →
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <span class="text-white font-bold text-xl">SEOAI</span>
                    <p class="text-sm mt-1">© 2026 SEO Location Intelligence Platform</p>
                </div>
                <div class="flex gap-6 text-sm">
                    <a href="/dashboard" class="hover:text-white transition-colors">Dashboard</a>
                    <a href="/admin" class="hover:text-white transition-colors">Admin</a>
                    <a href="https://github.com/NORARAE/seoai" target="_blank" class="hover:text-white transition-colors">GitHub</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>

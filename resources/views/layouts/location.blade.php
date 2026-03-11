<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    <title>{{ $metaTitle ?? $title ?? 'Page' }}</title>
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    
    @if(isset($canonicalUrl))
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif

    @if(isset($isIndexable) && !$isIndexable)
    <meta name="robots" content="noindex, nofollow">
    @endif

    {{-- Basic Styles --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9fafb;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        .admin-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 0;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .admin-banner-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #dc2626 100%);
        }
        
        .admin-banner a {
            color: white;
            text-decoration: underline;
            margin-left: 0.5rem;
        }
        
        header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumbs {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .breadcrumbs a {
            color: #3b82f6;
            text-decoration: none;
        }
        
        .breadcrumbs a:hover {
            text-decoration: underline;
        }
        
        .breadcrumbs span {
            margin: 0 0.5rem;
        }
        
        main {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        h2 {
            font-size: 1.875rem;
            font-weight: 600;
            color: #1f2937;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #374151;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
        }
        
        p {
            margin-bottom: 1rem;
            color: #4b5563;
        }
        
        .section {
            margin-bottom: 2.5rem;
        }
        
        .section:last-child {
            margin-bottom: 0;
        }
        
        .internal-links {
            background: #f3f4f6;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 2.5rem;
        }
        
        .internal-links h2 {
            margin-top: 0;
            font-size: 1.5rem;
        }
        
        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .link-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 1rem;
            text-decoration: none;
            color: #1f2937;
            transition: all 0.2s;
        }
        
        .link-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }
        
        .link-card-title {
            font-weight: 600;
            color: #3b82f6;
            margin-bottom: 0.25rem;
        }
        
        .link-card-meta {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        footer {
            text-align: center;
            padding: 2rem 0;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            main {
                padding: 1.5rem;
            }
            
            .link-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Schema.org JSON-LD Structured Data --}}
    @if(isset($schemas) && is_array($schemas))
        @foreach($schemas as $schema)
            @if($schema)
                <script type="application/ld+json">
                    {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
                </script>
            @endif
        @endforeach
    @endif

    @stack('styles')
</head>
<body>
    @yield('admin-banner')

    <header>
        <div class="container">
            @yield('breadcrumbs')
        </div>
    </header>

    <div class="container">
        <main>
            @yield('content')
        </main>
    </div>

    <footer>
        <div class="container">
            @yield('footer')
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

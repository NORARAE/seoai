<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    <title>{{ $metaTitle ?? $title ?? 'Page' }}</title>
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    
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
            line-height: 1.75;
            color: #1f2937;
            background-color: #f5f7fa;
            font-size: 18px;
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
            border-bottom: 2px solid #e5e7eb;
            padding: 1.25rem 0;
            margin-bottom: 2.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .breadcrumbs {
            font-size: 1rem;
            color: #6b7280;
            font-weight: 500;
            letter-spacing: 0.01em;
        }
        
        .breadcrumbs a {
            color: #2563eb;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumbs a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
        
        .breadcrumbs a:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
            border-radius: 2px;
        }
        
        .breadcrumbs span {
            margin: 0 0.625rem;
            color: #9ca3af;
        }
        
        main {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            margin-bottom: 3rem;
            border: 1px solid #e5e7eb;
        }
        
        h1 {
            font-size: 2.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2rem;
            line-height: 1.25;
            letter-spacing: -0.025em;
        }
        
        h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #1f2937;
            margin-top: 3rem;
            margin-bottom: 1.25rem;
            line-height: 1.35;
            letter-spacing: -0.015em;
        }
        
        h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #374151;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        
        p {
            margin-bottom: 1.25rem;
            color: #374151;
            line-height: 1.8;
            max-width: 75ch;
        }
        
        .section {
            margin-bottom: 3rem;
            padding-bottom: 2rem;
        }
        
        .section:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section + .section {
            border-top: 1px solid #f3f4f6;
            padding-top: 2rem;
        }
        
        .internal-links {
            background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
            border-radius: 0.75rem;
            padding: 2.5rem 2rem;
            margin-top: 3rem;
            border: 1px solid #e5e7eb;
        }
        
        .internal-links h2 {
            margin-top: 0;
            font-size: 1.75rem;
            margin-bottom: 1.75rem;
            color: #111827;
        }
        
        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.25rem;
        }
        
        .link-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 0.625rem;
            padding: 1.5rem;
            text-decoration: none;
            color: #1f2937;
            transition: all 0.25s ease;
            display: block;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .link-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2);
            transform: translateY(-3px);
            background: #fafbff;
        }
        
        .link-card:focus {
            outline: 3px solid #3b82f6;
            outline-offset: 2px;
        }
        
        .link-card-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: #2563eb;
            margin-bottom: 0.625rem;
            display: block;
            line-height: 1.4;
        }
        
        .link-card-meta {
            font-size: 0.95rem;
            color: #6b7280;
            display: block;
            margin-top: 0.5rem;
            font-weight: 500;
        }
        
        footer {
            text-align: center;
            padding: 2rem 0;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            body {
                font-size: 17px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            h2 {
                font-size: 1.625rem;
            }
            
            h3 {
                font-size: 1.375rem;
            }
            
            main {
                padding: 1.75rem;
                border-radius: 0.5rem;
            }
            
            .internal-links {
                padding: 1.75rem 1.25rem;
            }
            
            .link-grid {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            
            .breadcrumbs {
                font-size: 0.95rem;
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

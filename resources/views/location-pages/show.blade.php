@extends('layouts.location', [
    'title' => $page->title,
    'metaTitle' => $page->meta_title,
    'metaDescription' => $page->meta_description,
    'canonicalUrl' => $page->canonical_url,
    'isIndexable' => $page->is_indexable,
])

@section('admin-banner')
    @if($isAdmin)
        @php
            $showBanner = false;
            $bannerMessage = '';
            $bannerClass = 'admin-banner';
            
            if ($page->status === 'draft') {
                $showBanner = true;
                $bannerMessage = '🔒 DRAFT - This page is not published';
                $bannerClass = 'admin-banner-warning';
            } elseif ($page->status === 'archived') {
                $showBanner = true;
                $bannerMessage = '📦 ARCHIVED - This page is no longer active';
                $bannerClass = 'admin-banner-warning';
            } elseif ($page->content_quality_status === 'unreviewed') {
                $showBanner = true;
                $bannerMessage = '👀 UNREVIEWED - Content has not been reviewed yet';
            } elseif ($page->content_quality_status === 'excluded') {
                $showBanner = true;
                $bannerMessage = '🚫 EXCLUDED - This page will not be published';
                $bannerClass = 'admin-banner-warning';
            } elseif ($page->content_quality_status === 'edited') {
                $showBanner = true;
                $bannerMessage = '✏️ EDITED - Content has been modified and needs review';
            } elseif (!$page->is_indexable) {
                $showBanner = true;
                $bannerMessage = '🤖 NOINDEX - This page is hidden from search engines';
            }
        @endphp

        @if($showBanner)
            <div class="{{ $bannerClass }}">
                {{ $bannerMessage }}
                <a href="{{ url('/admin/location-pages/' . $page->id) }}" target="_blank">Edit in Admin</a>
            </div>
        @endif
    @endif
@endsection

@section('breadcrumbs')
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        @foreach($breadcrumbs as $index => $crumb)
            @if($index > 0)<span>/</span>@endif
            
            @if($crumb['url'])
                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
            @else
                <span>{{ $crumb['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endsection

@section('content')
    {{-- Main H1 --}}
    <h1>{{ $page->h1 }}</h1>

    {{-- Render Body Sections --}}
    @if($page->body_sections_json && is_array($page->body_sections_json))
        @foreach($page->body_sections_json as $section)
            @if(isset($section['type']) && $section['type'] !== 'internal_links')
                <div class="section section-{{ $section['type'] ?? 'default' }}">
                    @if(isset($section['heading']))
                        @if($loop->first)
                            <h2>{{ $section['heading'] }}</h2>
                        @else
                            <h2>{{ $section['heading'] }}</h2>
                        @endif
                    @endif

                    @if(isset($section['content']))
                        <p>{{ $section['content'] }}</p>
                    @endif
                </div>
            @endif
        @endforeach
    @endif

    {{-- Render Internal Links --}}
    @if($page->internal_links_json && isset($page->internal_links_json['links']) && count($page->internal_links_json['links']) > 0)
        <div class="internal-links">
            <h2>Related Service Areas</h2>
            
            <div class="link-grid">
                @foreach($page->internal_links_json['links'] as $link)
                    <a href="{{ $link['url'] ?? '#' }}" class="link-card">
                        <div class="link-card-title">
                            {{ $link['anchor'] ?? 'View Page' }}
                        </div>
                        
                        @if(isset($link['distance']))
                            <div class="link-card-meta">
                                📍 {{ number_format($link['distance'], 1) }} miles away
                            </div>
                        @elseif(isset($link['rel']) && $link['rel'] === 'parent')
                            <div class="link-card-meta">
                                🏠 County Hub
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@section('footer')
    <p>
        Page Type: {{ ucfirst(str_replace('_', ' ', $page->type)) }}
        @if($page->score)
            | Quality Score: {{ $page->score }}
        @endif
        @if($isAdmin)
            | Generated: {{ $page->generated_at?->format('M j, Y g:i A') ?? 'Unknown' }}
        @endif
    </p>
@endsection

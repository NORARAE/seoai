<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Search Preview</p>
        <div class="mt-3 space-y-2">
            <p class="text-lg font-semibold text-blue-700">{{ $payload->title }}</p>
            <p class="text-sm text-green-700">{{ $payload->canonical_url_suggestion ?: 'No canonical URL suggested.' }}</p>
            <p class="text-sm leading-6 text-gray-600">{{ $payload->meta_description ?: 'No meta description generated.' }}</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Workflow</p>
            <dl class="mt-3 space-y-2 text-sm text-gray-700">
                <div class="flex items-center justify-between gap-3">
                    <dt>Status</dt>
                    <dd class="font-medium">{{ str($payload->status)->replace('_', ' ')->title() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt>Publish</dt>
                    <dd class="font-medium">{{ str($payload->publish_status)->replace('_', ' ')->title() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt>Body Size</dt>
                    <dd class="font-medium">{{ $payload->formatted_body_length }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt>Sections</dt>
                    <dd class="font-medium">{{ $payload->section_count }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 md:col-span-2">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Section Outline</p>
            <div class="mt-3 grid gap-3 md:grid-cols-2">
                @forelse ($payload->preview_sections as $section)
                    <div class="rounded-lg border border-gray-200 bg-white p-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $section['heading'] }}</p>
                        <p class="mt-2 text-xs leading-5 text-gray-600">{{ $section['excerpt'] ?: 'No excerpt available.' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 md:col-span-2">No section headings were extracted from the generated body content.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Rendered HTML Preview</p>
        <article class="mt-4 max-w-none space-y-4 text-sm leading-7 text-gray-800 [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_p]:my-4 [&_ul]:my-4 [&_ul]:list-disc [&_ul]:pl-6">
            {!! $payload->preview_body_html !!}
        </article>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-gray-950 p-4 text-xs text-gray-100 shadow-sm">
            <p class="mb-3 font-semibold uppercase tracking-[0.2em] text-gray-400">Schema JSON-LD</p>
            <pre class="overflow-x-auto whitespace-pre-wrap">{{ json_encode($payload->schema_json_ld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: 'No schema generated.' }}</pre>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Internal Link Suggestions</p>
            <div class="mt-3 space-y-3 text-sm text-gray-700">
                @forelse (($payload->internal_link_suggestions ?? []) as $link)
                    <div class="rounded-lg border border-gray-200 p-3">
                        <p class="font-medium text-gray-900">{{ $link['title'] ?? $link['url'] ?? 'Suggested link' }}</p>
                        @if (!empty($link['url']))
                            <p class="mt-1 break-all text-xs text-blue-700">{{ $link['url'] }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No internal link suggestions were generated for this payload.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
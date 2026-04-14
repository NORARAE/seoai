@props(['faqs', 'heading' => 'Common Questions'])

<section class="faq-section" aria-label="{{ $heading }}">
    <h2 class="faq-section-heading">{{ $heading }}</h2>
    <div class="faq-list">
        @foreach($faqs as $faq)
        <div class="faq-item">
            <h3 class="faq-q">{{ $faq['question'] }}</h3>
            <p class="faq-a">{{ $faq['answer'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn($faq) => [
        '@type' => 'Question',
        'name'  => $faq['question'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => $faq['answer'],
        ],
    ])->values()->toArray(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

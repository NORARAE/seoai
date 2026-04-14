@props(['question', 'answer', 'detail' => null])

<div class="answer-block">
    <h3 class="ab-q">{{ $question }}</h3>
    <p class="ab-a">{{ $answer }}</p>
    @if($detail)
    <p class="ab-detail">{{ $detail }}</p>
    @endif
</div>

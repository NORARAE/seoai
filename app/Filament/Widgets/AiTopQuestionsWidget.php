<?php

namespace App\Filament\Widgets;

use App\Models\AiChatLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AiTopQuestionsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Top Questions (Last 50)';
    }

    protected function getDescription(): ?string
    {
        return 'Grouped by intent and repeated phrases to reveal user friction points.';
    }

    protected function getStats(): array
    {
        $recent = AiChatLog::query()
            ->latest('created_at')
            ->limit(50)
            ->get(['intent', 'user_message']);

        if ($recent->isEmpty()) {
            return [
                Stat::make('No AI questions yet', '0')
                    ->description('Start chatting on public pages or dashboard to populate insights.')
                    ->color('gray'),
            ];
        }

        $intentCounts = $recent
            ->groupBy(fn(AiChatLog $log) => $log->intent ?: 'general')
            ->map(fn($rows) => $rows->count())
            ->sortDesc()
            ->take(2);

        $topIntentLabel = (string) ($intentCounts->keys()->first() ?? 'general');
        $topIntentCount = (int) ($intentCounts->first() ?? 0);

        $secondIntentLabel = (string) ($intentCounts->keys()->skip(1)->first() ?? 'n/a');
        $secondIntentCount = (int) ($intentCounts->skip(1)->first() ?? 0);

        [$topPhrase, $topPhraseCount, $secondPhrase, $secondPhraseCount] = $this->extractTopPhrases($recent->pluck('user_message')->all());

        return [
            Stat::make('Top Intent', ucfirst($topIntentLabel))
                ->description($topIntentCount . ' of ' . $recent->count() . ' recent questions')
                ->color('warning'),

            Stat::make('2nd Intent', ucfirst($secondIntentLabel))
                ->description($secondIntentCount > 0 ? ($secondIntentCount . ' recent questions') : 'No secondary trend yet')
                ->color($secondIntentCount > 0 ? 'info' : 'gray'),

            Stat::make('Top Phrase', $topPhrase)
                ->description($topPhraseCount > 0 ? ($topPhraseCount . ' mentions in last 50') : 'No repeated phrase yet')
                ->color($topPhraseCount > 0 ? 'success' : 'gray'),

            Stat::make('2nd Phrase', $secondPhrase)
                ->description($secondPhraseCount > 0 ? ($secondPhraseCount . ' mentions in last 50') : 'No secondary phrase yet')
                ->color($secondPhraseCount > 0 ? 'info' : 'gray'),
        ];
    }

    /**
     * @return array{string,int,string,int}
     */
    private function extractTopPhrases(array $messages): array
    {
        $stopWords = [
            'the',
            'and',
            'for',
            'with',
            'that',
            'this',
            'from',
            'your',
            'have',
            'what',
            'how',
            'can',
            'are',
            'you',
            'about',
            'into',
            'when',
            'where',
            'why',
            'will',
            'not',
            'but',
            'our',
            'get',
            'does',
            'did',
            'its',
            'they',
            'them',
            'their',
            'just',
            'more',
            'than',
            'then',
            'after',
            'before',
            'should',
            'would',
            'could',
            'want',
            'need',
            'site',
            'score',
            'visibility',
        ];

        $counts = [];

        foreach ($messages as $message) {
            $clean = strtolower((string) $message);
            $clean = preg_replace('/[^a-z0-9\s]/', ' ', $clean) ?? '';
            $words = array_values(array_filter(preg_split('/\s+/', $clean) ?: [], function ($word) use ($stopWords) {
                return strlen($word) >= 3 && !in_array($word, $stopWords, true);
            }));

            for ($i = 0; $i < count($words) - 1; $i++) {
                $phrase = $words[$i] . ' ' . $words[$i + 1];
                $counts[$phrase] = ($counts[$phrase] ?? 0) + 1;
            }
        }

        arsort($counts);
        $phrases = array_keys($counts);
        $values = array_values($counts);

        $topPhrase = $phrases[0] ?? 'n/a';
        $topCount = $values[0] ?? 0;
        $secondPhrase = $phrases[1] ?? 'n/a';
        $secondCount = $values[1] ?? 0;

        return [$topPhrase, $topCount, $secondPhrase, $secondCount];
    }
}

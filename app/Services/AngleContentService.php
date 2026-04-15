<?php

namespace App\Services;

use App\Models\AngleContent;
use App\Models\ReachAngle;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class AngleContentService
{
    public function generate(ReachAngle $angle): array
    {
        $apiKey  = Setting::get('deepseek_api_key');
        $model   = Setting::get('deepseek_model', 'deepseek-chat');
        $baseUrl = Setting::get('deepseek_base_url', 'https://api.deepseek.com');

        if (! $apiKey) {
            throw new \Exception('DeepSeek API key is not configured. Go to Settings → API Settings.');
        }

        $this->checkCooldown($angle->id);

        $batch = (AngleContent::where('angle_id', $angle->id)->max('batch') ?? 0) + 1;

        $raw = $this->callApi($apiKey, $model, $baseUrl, $angle);

        $contents = [];
        foreach (['casual', 'story', 'factual'] as $style) {
            $contents[] = AngleContent::create([
                'angle_id'  => $angle->id,
                'batch'     => $batch,
                'style'     => $style,
                'content'   => $raw[$style] ?? '',
                'is_pinned' => false,
                'model'     => $model,
            ]);
        }

        $this->cleanup($angle->id);

        return $contents;
    }

    public function togglePin(AngleContent $content): AngleContent
    {
        $content->update(['is_pinned' => ! $content->is_pinned]);
        return $content->fresh();
    }

    private function checkCooldown(int $angleId): void
    {
        $last = AngleContent::where('angle_id', $angleId)->latest()->first();

        if ($last && $last->created_at->diffInMinutes(now()) < 5) {
            $wait = 5 - $last->created_at->diffInMinutes(now());
            throw new \Exception("Cooldown active. Wait {$wait} more minute(s) before generating again.");
        }
    }

    private function callApi(string $apiKey, string $model, string $baseUrl, ReachAngle $angle): array
    {
        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->withoutVerifying()
            ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                'model'           => $model,
                'messages'        => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user',   'content' => $this->userPrompt($angle)],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature'     => 0.8,
                'max_tokens'      => 800,
            ]);

        if (! $response->successful()) {
            throw new \Exception('API error: ' . $response->status() . ' — ' . $response->body());
        }

        $parsed = json_decode($response->json('choices.0.message.content'), true);

        if (! is_array($parsed) || ! isset($parsed['casual'], $parsed['story'], $parsed['factual'])) {
            throw new \Exception('Unexpected response format from API.');
        }

        return $parsed;
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
You are a Takaful content writer for a Malaysian insurance consultant.
Write compelling, authentic content for social media and client outreach in Bahasa Malaysia or English (match the angle language).
Always respond in valid JSON with exactly 3 keys: casual, story, factual.
Keep each value to 2–4 sentences maximum.
PROMPT;
    }

    private function userPrompt(ReachAngle $angle): string
    {
        $desc   = mb_substr($angle->description ?? '', 0, 500);
        $target = $angle->target_segment ?? 'general audience';

        return <<<PROMPT
Write 3 content variations for this Takaful reach angle:

Angle: {$angle->title}
Target audience: {$target}
Context: {$desc}

Return JSON with:
- casual: short, punchy, social caption style — feels like a WhatsApp message or Instagram caption
- story: mini narrative with a relatable scenario, emotional hook
- factual: data-driven, uses statistics or hard truths, creates urgency
PROMPT;
    }

    private function cleanup(int $angleId): void
    {
        $batches = AngleContent::where('angle_id', $angleId)
            ->where('is_pinned', false)
            ->selectRaw('batch')
            ->distinct()
            ->orderByDesc('batch')
            ->pluck('batch');

        if ($batches->count() > 5) {
            AngleContent::where('angle_id', $angleId)
                ->whereIn('batch', $batches->slice(5))
                ->where('is_pinned', false)
                ->delete();
        }
    }
}

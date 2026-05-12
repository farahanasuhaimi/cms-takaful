<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class StrategyAiService
{
    public function generate(array $params): array
    {
        $apiKey  = Setting::get('deepseek_api_key');
        $model   = Setting::get('deepseek_model', 'deepseek-chat');
        $baseUrl = Setting::get('deepseek_base_url', 'https://api.deepseek.com');

        if (! $apiKey) {
            throw new \Exception('DeepSeek API key is not configured. Go to Settings → API Settings.');
        }

        $isFlow = ($params['type'] === 'flow');
        $prompt = $isFlow ? $this->flowPrompt($params) : $this->scriptPrompt($params);

        $response = Http::withToken($apiKey)
            ->timeout(45)
            ->withoutVerifying()
            ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                'model'           => $model,
                'messages'        => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature'     => 0.75,
                'max_tokens'      => 1500,
            ]);

        if (! $response->successful()) {
            throw new \Exception('API error: ' . $response->status() . ' — ' . $response->body());
        }

        $parsed = json_decode($response->json('choices.0.message.content'), true);

        if (! is_array($parsed) || ! isset($parsed['title'])) {
            throw new \Exception('Unexpected response format from API.');
        }

        return $parsed;
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
You are a Takaful sales trainer specializing in the Malaysian market.
Generate practical, field-tested sales strategies for Takaful consultants.
Write in clear, actionable language. Mix Bahasa Malaysia phrases naturally where appropriate.
Always return valid JSON.
PROMPT;
    }

    private function scriptPrompt(array $p): string
    {
        $brief = $p['brief'] ?? '';
        return <<<PROMPT
Generate a sales script strategy for a Takaful consultant with these parameters:
- Category: {$p['category']}
- Channel: {$p['channel']}
- Audience: {$p['audience']}
- Difficulty: {$p['difficulty']}
- Additional context: {$brief}

Return JSON with:
{
  "title": "short descriptive title",
  "description": "1-2 sentence summary of when and how to use this",
  "content": "the full script/template the consultant will use — include specific lines to say, natural conversation flow, and Takaful-specific talking points"
}
PROMPT;
    }

    private function flowPrompt(array $p): string
    {
        $brief = $p['brief'] ?? '';
        return <<<PROMPT
Generate a multi-step sales flow strategy for a Takaful consultant:
- Category: {$p['category']}
- Channel: {$p['channel']}
- Audience: {$p['audience']}
- Difficulty: {$p['difficulty']}
- Additional context: {$brief}

Return JSON with:
{
  "title": "short descriptive title",
  "description": "1-2 sentence summary",
  "steps": [
    {
      "title": "step name",
      "script": "what to say/do at this step",
      "timing_note": "when to do this step (e.g. 'Day 1', 'Wait 2 days', 'Same conversation')",
      "branch_yes": "what to do if they respond positively",
      "branch_no": "what to do if no response or rejection"
    }
  ]
}
Include 3–6 steps. Each step should have concrete, usable scripts.
PROMPT;
    }
}

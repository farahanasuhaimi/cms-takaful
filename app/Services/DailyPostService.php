<?php

namespace App\Services;

use App\Models\DailyPost;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailyPostService
{
    public function generate(DailyPost $post): DailyPost
    {
        $post->loadMissing('reachAngle', 'planProduct');
        $apiKey  = Setting::get('deepseek_api_key');
        $model   = Setting::get('deepseek_model', 'deepseek-chat');
        $baseUrl = Setting::get('deepseek_base_url', 'https://api.deepseek.com');

        if (! $apiKey) {
            throw new \Exception('DeepSeek API key is not configured. Go to Settings → API Settings.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                'model'           => $model,
                'messages'        => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user',   'content' => $this->userPrompt($post)],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature'     => 0.8,
                'max_tokens'      => 900,
            ]);

        if (! $response->successful()) {
            Log::error('DailyPostService API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('AI generation failed. Please check your API settings.');
        }

        $parsed = json_decode($response->json('choices.0.message.content'), true);

        if (! is_array($parsed) || ! isset($parsed['caption'], $parsed['image_prompts']) || ! is_array($parsed['image_prompts'])) {
            throw new \Exception('Unexpected response format from API.');
        }

        $post->update([
            'caption'      => $parsed['caption'],
            'image_prompt' => array_values(array_slice($parsed['image_prompts'], 0, 2)),
            'status'       => 'ready',
        ]);

        return $post->fresh();
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
You are a Takaful content writer for a Malaysian insurance consultant.
Write engaging social media content for the given platform and topic.
Always respond in valid JSON with exactly 2 keys: caption, image_prompts.
- caption: ready-to-post social media text, in Bahasa Malaysia or English matching the topic's language, 3-5 sentences max.
- image_prompts: an array of exactly 2 image prompt options (in English). First: a neutral, clean visual — no people, focus on object, setting, or concept, calm colours. Second: an emotional version — a relatable human moment, warm expression, personal scene that creates an emotional connection.
PROMPT;
    }

    private function userPrompt(DailyPost $post): string
    {
        $platformHints = [
            'instagram' => 'Instagram post — punchy caption, can include 2-3 relevant hashtags',
            'facebook'  => 'Facebook post — slightly longer, conversational, can include a question to drive engagement',
            'whatsapp'  => 'WhatsApp broadcast — personal, warm, short, no hashtags',
            'tiktok'    => 'TikTok caption — very short, hook-first, trending energy, 1-2 hashtags',
        ];

        $hint = $platformHints[$post->platform] ?? 'social media post';

        $angleBlock = '';
        if ($post->reachAngle) {
            $a = $post->reachAngle;
            $angleBlock = "\nReach Angle: {$a->title}";
            if ($a->target_segment) $angleBlock .= "\nTarget Segment: {$a->target_segment}";
            if ($a->description)    $angleBlock .= "\nAngle Description: {$a->description}";
            if ($a->notes)          $angleBlock .= "\nAdditional Notes: {$a->notes}";
            $angleBlock .= "\n";
        }

        $productBlock = '';
        if ($post->planProduct) {
            $p = $post->planProduct;
            $typeLabel = ucfirst(str_replace('_', ' ', $p->plan_type));
            $productBlock = "\nProduct: {$p->name} ({$typeLabel})";
            if (! empty($p->attributes)) {
                foreach ($p->attributes as $attr) {
                    if (! empty($attr['key']) && isset($attr['value'])) {
                        $productBlock .= "\n- {$attr['key']}: {$attr['value']}";
                    }
                }
            }
            if ($p->notes) $productBlock .= "\nProduct Notes: {$p->notes}";
            $productBlock .= "\n";
        }

        return <<<PROMPT
Write content for this daily Takaful post:

Platform: {$hint}
Topic: {$post->topic}{$angleBlock}{$productBlock}
Return JSON with:
- caption: the actual post caption, ready to copy-paste, written for the target segment and angle above if provided
- image_prompts: array of exactly 2 image descriptions — first neutral/clean, second emotional/human
PROMPT;
    }
}

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
                'max_tokens'      => 600,
            ]);

        if (! $response->successful()) {
            Log::error('DailyPostService API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('AI generation failed. Please check your API settings.');
        }

        $parsed = json_decode($response->json('choices.0.message.content'), true);

        if (! is_array($parsed) || ! isset($parsed['caption'], $parsed['image_prompt'])) {
            throw new \Exception('Unexpected response format from API.');
        }

        $post->update([
            'caption'      => $parsed['caption'],
            'image_prompt' => $parsed['image_prompt'],
            'status'       => 'ready',
        ]);

        return $post->fresh();
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
You are a Takaful content writer for a Malaysian insurance consultant.
Write engaging social media content for the given platform and topic.
Always respond in valid JSON with exactly 2 keys: caption, image_prompt.
- caption: ready-to-post social media text, in Bahasa Malaysia or English matching the topic's language, 3-5 sentences max.
- image_prompt: a short visual description for generating or sourcing an image (in English), 1-2 sentences, describe scene, mood, colours.
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

        return <<<PROMPT
Write content for this daily Takaful post:

Platform: {$hint}
Topic: {$post->topic}

Return JSON with:
- caption: the actual post caption, ready to copy-paste
- image_prompt: describe an image to accompany this post (visual, colours, mood, subject)
PROMPT;
    }
}

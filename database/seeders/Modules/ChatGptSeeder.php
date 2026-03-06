<?php

namespace Database\Seeders\Modules;

/**
 * ChatGPT Service Seeder — AI Integration
 * Seeds: AI templates, prompt categories, model configurations
 */
class ChatGptSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'ChatGPT';

    protected function seed(): void
    {
        if ($this->tableExists('ai_models')) {
            $models = [
                ['name' => 'GPT-4', 'slug' => 'gpt-4', 'provider' => 'openai', 'max_tokens' => 8192, 'is_active' => 1],
                ['name' => 'GPT-4 Turbo', 'slug' => 'gpt-4-turbo', 'provider' => 'openai', 'max_tokens' => 128000, 'is_active' => 1],
                ['name' => 'GPT-3.5 Turbo', 'slug' => 'gpt-3.5-turbo', 'provider' => 'openai', 'max_tokens' => 4096, 'is_active' => 1],
                ['name' => 'Claude 3 Opus', 'slug' => 'claude-3-opus', 'provider' => 'anthropic', 'max_tokens' => 200000, 'is_active' => 0],
                ['name' => 'Gemini Pro', 'slug' => 'gemini-pro', 'provider' => 'google', 'max_tokens' => 32768, 'is_active' => 0],
            ];
            foreach ($models as $m) {
                $this->upsert('ai_models', ['slug' => $m['slug']], $m);
            }
        }

        if ($this->tableExists('prompt_categories')) {
            $categories = [
                ['name' => 'Sales', 'slug' => 'sales', 'description' => 'Sales-related AI prompts'],
                ['name' => 'HR', 'slug' => 'hr', 'description' => 'Human resources prompts'],
                ['name' => 'Finance', 'slug' => 'finance', 'description' => 'Financial analysis prompts'],
                ['name' => 'Marketing', 'slug' => 'marketing', 'description' => 'Marketing content prompts'],
                ['name' => 'Customer Support', 'slug' => 'customer_support', 'description' => 'Support response prompts'],
                ['name' => 'Legal', 'slug' => 'legal', 'description' => 'Legal document drafting'],
                ['name' => 'General', 'slug' => 'general', 'description' => 'General-purpose prompts'],
            ];
            foreach ($categories as $c) {
                $this->upsert('prompt_categories', ['slug' => $c['slug']], $c);
            }
        }

        if ($this->tableExists('chatgpt_settings')) {
            $settings = [
                ['key' => 'default_model', 'value' => 'gpt-3.5-turbo'],
                ['key' => 'max_tokens_per_request', 'value' => '2048'],
                ['key' => 'temperature', 'value' => '0.7'],
                ['key' => 'daily_token_limit', 'value' => '100000'],
                ['key' => 'enable_chat_history', 'value' => '1'],
                ['key' => 'enable_streaming', 'value' => '1'],
            ];
            foreach ($settings as $s) {
                $this->upsert('chatgpt_settings', ['key' => $s['key']], $s);
            }
        }
    }
}

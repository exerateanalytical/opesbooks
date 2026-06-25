<?php

return [
    // Global fallback key (per-company keys in ai_configs take precedence).
    'gemini_key'      => env('GEMINI_API_KEY'),
    'gemini_model'    => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    'ollama_url'      => env('OLLAMA_URL', 'http://localhost:11434'),
    'ollama_model'    => env('OLLAMA_MODEL', 'gemma3:1b'),
    'timeout_online'  => env('AI_TIMEOUT_ONLINE', 12),
    'timeout_offline' => env('AI_TIMEOUT_OFFLINE', 30),
];

<?php

namespace App\Services;

use App\Models\AiConfig;
use Illuminate\Support\Facades\Http;

/**
 * Dual-mode AI: Gemini Flash (online) with an optional local Ollama fallback
 * (offline). On shared hosting Ollama is typically unavailable, so the service
 * degrades gracefully: every method returns a structured result and never throws.
 */
class AiService
{
    private ?string $geminiKey;
    private bool $ollamaEnabled;
    private string $ollamaModel;

    public function __construct(?AiConfig $config = null)
    {
        $this->geminiKey     = $config?->gemini_api_key ?: config('ai.gemini_key');
        $this->ollamaEnabled = $config?->ollama_enabled ?? false;
        $this->ollamaModel   = $config?->ollama_model ?: config('ai.ollama_model');
    }

    public function geminiAvailable(): bool
    {
        return ! empty($this->geminiKey);
    }

    public function ollamaAvailable(): bool
    {
        if (! $this->ollamaEnabled) {
            return false;
        }
        try {
            return Http::timeout(2)->get(rtrim(config('ai.ollama_url'), '/') . '/api/tags')->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    /** Overall status for the UI badge. */
    public function status(): array
    {
        $online = $this->geminiAvailable();
        $offline = ! $online && $this->ollamaAvailable();
        return [
            'available' => $online || $offline,
            'mode'      => $online ? 'online' : ($offline ? 'offline' : 'unavailable'),
            'model'     => $online ? config('ai.gemini_model') : ($offline ? $this->ollamaModel : null),
        ];
    }

    public function categorize(string $description, float $amount): array
    {
        return $this->run('categorize', <<<PROMPT
        You are an OHADA SYSCOHADA accounting expert for Cameroonian SMEs.
        Categorize this transaction into the correct SYSCOHADA account. Return ONLY valid JSON.
        Transaction: "{$description}"  Amount: {$amount} XAF
        JSON: {"account_code":"6230","account_name":"...","account_class":"6","confidence":0.0,"entry_type":"debit|credit","explanation":"français"}
        PROMPT);
    }

    public function checkDsf(array $entries): array
    {
        $json = json_encode(array_slice($entries, 0, 50));
        return $this->run('dsf_check', <<<PROMPT
        You are an OHADA/DGI Cameroun tax-compliance expert. Review these journal entries for DSF filing issues. Return ONLY valid JSON.
        Entries: {$json}
        JSON: {"issues":[{"severity":"high|medium|low","account_code":"","description":"français","recommendation":"français","estimated_impact_xaf":0}],"summary":"français","ready_for_dsf":true}
        PROMPT);
    }

    public function detectAnomalies(array $entries): array
    {
        $json = json_encode(array_slice($entries, 0, 80));
        return $this->run('anomaly', <<<PROMPT
        You are an accounting anomaly/fraud detector for a Cameroonian SME. Find duplicates, unusual amounts, wrong accounts. Return ONLY valid JSON.
        Entries: {$json}
        JSON: {"anomalies":[{"type":"duplicate|unusual_amount|wrong_account|suspicious","entry_id":0,"description":"français","severity":"high|medium|low"}],"total_found":0}
        PROMPT);
    }

    public function naturalQuery(string $question, array $context): array
    {
        $ctx = json_encode($context);
        return $this->run('query', <<<PROMPT
        You are the financial assistant for a Cameroonian SME using OPESBooks (XAF, SYSCOHADA).
        Answer the user's question using ONLY the provided data context. Be concise, in the user's language. Return ONLY valid JSON.
        Question: "{$question}"
        Data context: {$ctx}
        JSON: {"answer":"...","figures":[{"label":"...","value":"... XAF"}]}
        PROMPT);
    }

    // ── engine ───────────────────────────────────────────────────────────────

    private function run(string $feature, string $prompt): array
    {
        $start = microtime(true);

        if ($this->geminiAvailable()) {
            $data = $this->callGemini($prompt);
            if ($data !== null) {
                return $this->ok($data, config('ai.gemini_model'), true, $start);
            }
        }

        if ($this->ollamaAvailable()) {
            $data = $this->callOllama($prompt);
            if ($data !== null) {
                return $this->ok($data, $this->ollamaModel, false, $start);
            }
        }

        return [
            'ok'     => false,
            'mode'   => 'unavailable',
            'reason' => $this->geminiAvailable()
                ? 'AI request failed.'
                : 'AI not configured — add a Gemini API key in Settings, or enable a local model.',
        ];
    }

    private function ok(array $data, ?string $model, bool $online, float $start): array
    {
        return [
            'ok'              => true,
            'mode'            => $online ? 'online' : 'offline',
            'model'           => $model,
            'response_time_ms'=> (int) round((microtime(true) - $start) * 1000),
            'data'            => $data,
        ];
    }

    private function callGemini(string $prompt): ?array
    {
        try {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
                . config('ai.gemini_model') . ':generateContent?key=' . $this->geminiKey;

            $res = Http::timeout(config('ai.timeout_online'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['responseMimeType' => 'application/json'],
                ]);

            if (! $res->successful()) {
                return null;
            }
            $text = $res->json('candidates.0.content.parts.0.text');
            return $text ? json_decode($text, true) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function callOllama(string $prompt): ?array
    {
        try {
            $res = Http::timeout(config('ai.timeout_offline'))
                ->post(rtrim(config('ai.ollama_url'), '/') . '/api/generate', [
                    'model'  => $this->ollamaModel,
                    'prompt' => $prompt,
                    'stream' => false,
                    'format' => 'json',
                ]);
            if (! $res->successful()) {
                return null;
            }
            return json_decode($res->json('response'), true);
        } catch (\Throwable) {
            return null;
        }
    }
}

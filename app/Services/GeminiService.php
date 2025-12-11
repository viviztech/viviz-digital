<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Generate product description and tags using Gemini Vision capabilities.
     *
     * @param string $title
     * @param string|null $imagePath Relative path in storage
     * @return array|null Returns ['description' => string, 'tags' => string] or null on failure
     */
    public function generateProductMetadata(string $title, ?string $imagePath): ?array
    {
        if (!$this->apiKey) {
            Log::error('Gemini API Key is missing.');
            return null;
        }

        $parts = [
            [
                'text' => "You are an expert digital asset marketer. Analyze the provided product image and title: '{$title}'. 
            
            1. Write a compelling, SEO-optimized HTML description (max 200 words). Use <p>, <ul>, <li> tags for formatting.
            2. Generate 10-15 relevant, high-traffic comma-separated tags.
            
            Return the response in valid raw JSON format without markdown code blocks:
            {
                \"description\": \"<p>...</p>\",
                \"tags\": \"tag1, tag2, tag3\"
            }"
            ]
        ];

        if ($imagePath) {
            // Check if it's a temporary file (Livewire) or stored file
            if (Storage::disk('public')->exists($imagePath)) {
                $mimeType = Storage::disk('public')->mimeType($imagePath);
                $imageContent = base64_encode(Storage::disk('public')->get($imagePath));
                Log::info("Gemini: Using public storage image: $imagePath ($mimeType)");
            } elseif (Storage::disk('local')->exists('livewire-tmp/' . $imagePath)) {
                // Handle livewire temp file if path matches
                $path = 'livewire-tmp/' . $imagePath;
                $mimeType = Storage::disk('local')->mimeType($path);
                $imageContent = base64_encode(Storage::disk('local')->get($path));
                Log::info("Gemini: Using livewire temp image: $path");
            } else {
                Log::warning("Gemini: Image path found but file not accessible: $imagePath");
                // Proceed without image rather than failing?
                // Let's try to proceed text-only if image fails
            }

            if (isset($imageContent)) {
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $imageContent
                    ]
                ];
            }
        }

        try {
            Log::info("Gemini: Sending request to API...");
            $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1000,
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error Body: ' . $response->body());
                return null;
            }

            Log::info("Gemini: Success response received.");

            $json = $response->json();
            $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$rawText) {
                return null;
            }

            // Cleanup potentially wrapped markdown JSON if the model ignores the responseMimeType
            $cleanedJson = str_replace(['```json', '```'], '', $rawText);
            return json_decode($cleanedJson, true);

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * Expand a search query into semantic synonyms using Gemini.
     * 
     * @param string $query
     * @return array
     */
    public function expandSearchQuery(string $query): array
    {
        if (!$this->apiKey || empty($query)) {
            return [$query];
        }

        try {
            // Check cache first to save API calls
            return \Illuminate\Support\Facades\Cache::remember("search_expansion_3_{$query}", 3600, function () use ($query) {
                $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "You are a smart search engine assistance. 
                                GENERATE 5-8 semantic synonyms or related keywords for the search query: '{$query}'.
                                Focus on what a user might be looking for in a digital asset marketplace.
                                Return ONLY a JSON array of strings. Example: [\"keyword1\", \"keyword2\"]"
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->failed()) {
                    return [$query];
                }

                $json = $response->json();
                $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                $cleanedJson = str_replace(['```json', '```'], '', $text);
                $synonyms = json_decode($cleanedJson, true) ?? [];

                // Merge original query to ensure it's included
                return array_unique(array_merge([$query], $synonyms));
            });

        } catch (\Exception $e) {
            Log::error("Gemini Search Expansion Failed: " . $e->getMessage());
            return [$query];
        }
    }
}

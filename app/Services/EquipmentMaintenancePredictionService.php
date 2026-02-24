<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentMaintenanceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * AI-Powered Equipment Maintenance Prediction Service
 * Uses Claude API to predict optimal maintenance schedules
 */
class EquipmentMaintenancePredictionService
{
    private const API_ENDPOINT = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-sonnet-4-20250514';

    /**
     * Predict maintenance schedule for equipment using AI
     * 
     * @param Equipment $equipment
     * @return array ['days' => int, 'reasoning' => string, 'confidence' => string]
     */
    public function predictMaintenanceSchedule(Equipment $equipment): array
    {
        try {
            // Prepare equipment data for AI analysis
            $equipmentData = $this->prepareEquipmentData($equipment);
            
            // Call Claude API for prediction
            $prediction = $this->callClaudeAPI($equipmentData);
            
            // Validate and return prediction
            return $this->validatePrediction($prediction);
            
        } catch (\Exception $e) {
            Log::error('AI Maintenance Prediction Error: ' . $e->getMessage());
            
            // Fallback to rule-based prediction
            return $this->fallbackPrediction($equipment);
        }
    }

    /**
     * Prepare equipment data for AI analysis
     */
    private function prepareEquipmentData(Equipment $equipment): array
    {
        $age = $equipment->acquisition_date 
            ? Carbon::parse($equipment->acquisition_date)->diffInDays(Carbon::today())
            : 0;

        return [
            'article' => $equipment->article,
            'classification' => $equipment->classification ?? 'General Equipment',
            'description' => $equipment->description ?? 'No description',
            'condition' => $equipment->condition,
            'unit_value' => $equipment->unit_value,
            'age_in_days' => $age,
            'location' => $equipment->location ?? 'Not specified',
            'last_maintenance' => $equipment->last_maintenance_check 
                ? Carbon::parse($equipment->last_maintenance_check)->diffInDays(Carbon::today()) . ' days ago'
                : 'Never',
            'maintenance_history_count' => $equipment->maintenanceLogs()->count(),
            'current_warnings' => $equipment->activeWarnings()->count()
        ];
    }

    /**
     * Call Claude API for maintenance prediction
     */
    private function callClaudeAPI(array $equipmentData): array
    {
        $prompt = $this->buildPrompt($equipmentData);
        
        $response = $this->makeAPIRequest($prompt);
        
        return $this->parseAPIResponse($response);
    }

    /**
     * Build AI prompt for maintenance prediction
     */
    private function buildPrompt(array $data): string
    {
        // Sanitize data to prevent JSON issues
        $article = $this->sanitizeString($data['article']);
        $classification = $this->sanitizeString($data['classification']);
        $description = $this->sanitizeString($data['description']);
        $location = $this->sanitizeString($data['location']);
        
        return <<<PROMPT
You are an expert equipment maintenance scheduler for a Philippine government institution. Analyze the following equipment and predict the optimal number of days until the next maintenance check.

Equipment Details:
- Name: {$article}
- Classification: {$classification}
- Description: {$description}
- Current Condition: {$data['condition']}
- Value: ₱{$data['unit_value']}
- Age: {$data['age_in_days']} days
- Location: {$location}
- Last Maintenance: {$data['last_maintenance']}
- Maintenance History: {$data['maintenance_history_count']} recorded checks
- Active Warnings: {$data['current_warnings']}

Consider these factors:
1. Equipment type and typical maintenance intervals
2. Current condition and age
3. Value and criticality
4. Usage patterns and environment
5. Philippine climate conditions (tropical, humid)
6. Government institution requirements

Respond ONLY in JSON format:
{
  "days": <number between 7 and 180>,
  "reasoning": "<brief explanation in 2-3 sentences>",
  "confidence": "<high|medium|low>",
  "risk_factors": ["<factor1>", "<factor2>"],
  "recommendations": "<brief maintenance tips>"
}

Rules:
- High-value equipment (>₱50,000): 14-60 days
- Electronic equipment: 30-90 days
- Mechanical equipment: 30-120 days
- Office furniture: 90-180 days
- Unserviceable equipment: 7-14 days (urgent inspection)
- Never suggest less than 7 days or more than 180 days
PROMPT;
    }

    /**
     * Make HTTP request to Claude API
     */
    private function makeAPIRequest(string $prompt): array
    {
        $ch = curl_init(self::API_ENDPOINT);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'anthropic-version: 2023-06-01'
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => self::MODEL,
                'max_tokens' => 1000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ])
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("API request failed with code: {$httpCode}");
        }

        return json_decode($response, true);
    }

    /**
     * Parse API response and extract prediction
     */
    private function parseAPIResponse(array $response): array
    {
        if (!isset($response['content'][0]['text'])) {
            throw new \Exception('Invalid API response structure');
        }

        $text = $response['content'][0]['text'];
        
        // Extract JSON from response
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $json = json_decode($matches[0], true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }
        }

        throw new \Exception('Could not parse JSON from API response');
    }

    /**
     * Validate AI prediction and ensure safety constraints
     */
    private function validatePrediction(array $prediction): array
    {
        // Ensure days is within acceptable range
        $days = isset($prediction['days']) ? (int)$prediction['days'] : 30;
        $days = max(7, min(180, $days)); // Clamp between 7 and 180 days

        return [
            'days' => $days,
            'reasoning' => $prediction['reasoning'] ?? 'Standard maintenance schedule',
            'confidence' => $prediction['confidence'] ?? 'medium',
            'risk_factors' => $prediction['risk_factors'] ?? [],
            'recommendations' => $prediction['recommendations'] ?? 'Regular inspection recommended'
        ];
    }

    /**
     * Fallback rule-based prediction if AI fails
     */
    private function fallbackPrediction(Equipment $equipment): array
    {
        $days = 30; // Default
        $reasoning = 'Using standard maintenance schedule';

        // Rule-based logic
        if ($equipment->condition === 'Unserviceable') {
            $days = 7;
            $reasoning = 'Unserviceable equipment requires urgent inspection';
        } elseif ($equipment->unit_value > 100000) {
            $days = 30;
            $reasoning = 'High-value equipment requires frequent monitoring';
        } elseif ($equipment->unit_value > 50000) {
            $days = 45;
            $reasoning = 'Medium-value equipment with moderate maintenance needs';
        } elseif (stripos($equipment->article, 'computer') !== false || 
                  stripos($equipment->article, 'printer') !== false) {
            $days = 60;
            $reasoning = 'Electronic equipment with standard maintenance cycle';
        } else {
            $days = 90;
            $reasoning = 'General equipment with extended maintenance interval';
        }

        return [
            'days' => $days,
            'reasoning' => $reasoning,
            'confidence' => 'medium',
            'risk_factors' => ['AI prediction unavailable'],
            'recommendations' => 'Monitor equipment condition regularly'
        ];
    }

    /**
     * Re-predict maintenance schedule after maintenance action
     */
    public function repredictAfterMaintenance(Equipment $equipment, string $actionTaken, string $conditionAfter): array
    {
        try {
            // Sanitize all input strings to prevent JSON parsing errors
            $article = $this->sanitizeString($equipment->article);
            $actionTaken = $this->sanitizeString($actionTaken);
            $classification = $this->sanitizeString($equipment->classification ?? 'General');
            
            $age = $equipment->acquisition_date 
                ? Carbon::parse($equipment->acquisition_date)->diffInDays(Carbon::today()) 
                : 0;
            
            $prompt = <<<PROMPT
Equipment maintenance has just been completed. Predict the optimal days until the NEXT maintenance check.

Equipment Information:
- Name: {$article}
- Classification: {$classification}
- Age: {$age} days
- Value: ₱{$equipment->unit_value}

Recent Maintenance:
- Action Taken: {$actionTaken}
- Condition After: {$conditionAfter}

Based on this recent maintenance, predict the optimal number of days until the next maintenance check.

Respond ONLY in JSON format:
{
  "days": <number between 7 and 180>,
  "reasoning": "<brief explanation>",
  "confidence": "<high|medium|low>"
}

Rules:
- Consider the condition after maintenance
- High-value equipment needs frequent monitoring
- Newly serviced equipment can have longer intervals
- Never suggest less than 7 days or more than 180 days
PROMPT;

            $response = $this->makeAPIRequest($prompt);
            $prediction = $this->parseAPIResponse($response);
            
            return $this->validatePrediction($prediction);
            
        } catch (\Exception $e) {
            Log::error('AI Re-prediction Error: ' . $e->getMessage());
            return $this->fallbackPrediction($equipment);
        }
    }

    /**
     * Sanitize string to prevent JSON parsing issues
     * Removes or escapes characters that could break JSON
     */
    private function sanitizeString(?string $input): string
    {
        if (empty($input)) {
            return 'Not specified';
        }
        
        // Remove control characters and normalize whitespace
        $sanitized = preg_replace('/[\x00-\x1F\x7F]/u', '', $input);
        
        // Replace problematic characters
        $sanitized = str_replace(['"', "'", "\n", "\r", "\t"], ['', '', ' ', ' ', ' '], $sanitized);
        
        // Trim and limit length
        $sanitized = trim($sanitized);
        $sanitized = mb_substr($sanitized, 0, 500);
        
        return $sanitized ?: 'Not specified';
    }

    /**
     * Get maintenance urgency level
     */
    public function getMaintenanceUrgency(Equipment $equipment): string
    {
        if (!$equipment->maintenance_schedule_end) {
            return 'unknown';
        }

        $daysUntil = Carbon::today()->diffInDays($equipment->maintenance_schedule_end, false);

        if ($daysUntil < 0) {
            return abs($daysUntil) > 30 ? 'critical' : 'overdue';
        } elseif ($daysUntil <= 7) {
            return 'due_soon';
        } elseif ($daysUntil <= 14) {
            return 'upcoming';
        } else {
            return 'scheduled';
        }
    }
}
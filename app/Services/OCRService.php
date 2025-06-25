<?php

namespace App\Services;

use App\Models\OCRResult;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class OCRService
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.ocr_space.api_key');
    }

    public function processFile(OCRResult $ocrResult): bool
    {
        try {
            $filePath = storage_path('app/public/' . $ocrResult->file_path);
            
            $response = $this->client->request('POST', 'https://api.ocr.space/parse/image', [
                'headers' => [
                    'apiKey' => $this->apiKey
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => $ocrResult->filename
                    ],
                    [
                        'name' => 'language',
                        'contents' => 'eng'
                    ],
                    [
                        'name' => 'detectOrientation',
                        'contents' => 'true'
                    ],
                    [
                        'name' => 'scale',
                        'contents' => 'true'
                    ]
                ]
            ]);

            $apiResponse = json_decode($response->getBody(), true);
            
            if ($this->isSuccessfulResponse($apiResponse)) {
                $extractedText = $this->extractTextFromResponse($apiResponse);
                
                $ocrResult->update([
                    'extracted_text' => $extractedText,
                    'status' => 'completed',
                    'api_response' => $apiResponse
                ]);
                
                return true;
            } else {
                $this->handleFailedResponse($ocrResult, $apiResponse);
                return false;
            }

        } catch (RequestException $e) {
            Log::error('OCR API request failed', [
                'file_id' => $ocrResult->id,
                'error' => $e->getMessage()
            ]);
            
            $ocrResult->update([
                'status' => 'failed',
                'api_response' => ['error' => $e->getMessage()]
            ]);
            
            return false;
        }
    }

    private function isSuccessfulResponse(array $response): bool
    {
        return isset($response['ParsedResults']) && 
               !empty($response['ParsedResults']) && 
               empty($response['ErrorMessage']);
    }

    private function extractTextFromResponse(array $response): string
    {
        $extractedText = '';
        foreach ($response['ParsedResults'] as $result) {
            $extractedText .= $result['ParsedText'] . "\n\n";
        }
        return trim($extractedText);
    }

    private function handleFailedResponse(OCRResult $ocrResult, array $response): void
    {
        $errorMessage = $response['ErrorMessage'] ?? 'Unknown error occurred';
        
        Log::error('OCR processing failed', [
            'file_id' => $ocrResult->id,
            'error' => $errorMessage,
            'response' => $response
        ]);
        
        $ocrResult->update([
            'status' => 'failed',
            'api_response' => $response
        ]);
    }
}
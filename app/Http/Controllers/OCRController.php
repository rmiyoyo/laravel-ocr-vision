<?php

namespace App\Http\Controllers;

use App\Models\OCRResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OCRController extends Controller
{
    public function index()
    {
        return view('ocr.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120' // 5MB max
        ]);

        try {
            $file = $request->file('file');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            
            // Store file in public storage
            $filePath = $file->storeAs('ocr-uploads', $filename, 'public');
            
            // Create OCR result record
            $ocrResult = OCRResult::create([
                'filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'status' => 'processing'
            ]);

            // Process OCR
            $this->processOCR($ocrResult);

            return redirect()->route('ocr.result', $ocrResult->id)
                           ->with('success', 'File uploaded and processed successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    private function processOCR(OCRResult $ocrResult)
    {
        try {
            $client = new Client();
            $filePath = storage_path('app/public/' . $ocrResult->file_path);
            
            $response = $client->request('POST', 'https://api.ocr.space/parse/image', [
                'headers' => [
                    'apiKey' => env('OCR_SPACE_API_KEY', 'helloworld')
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
                        'name' => 'isOverlayRequired',
                        'contents' => 'false'
                    ]
                ]
            ]);

            $apiResponse = json_decode($response->getBody(), true);
            
            if (isset($apiResponse['ParsedResults']) && !empty($apiResponse['ParsedResults'])) {
                $extractedText = '';
                foreach ($apiResponse['ParsedResults'] as $result) {
                    $extractedText .= $result['ParsedText'] . "\n";
                }
                
                $ocrResult->update([
                    'extracted_text' => trim($extractedText),
                    'status' => 'completed',
                    'api_response' => $apiResponse
                ]);
            } else {
                $errorMessage = $apiResponse['ErrorMessage'] ?? 'Unknown error occurred';
                $ocrResult->update([
                    'status' => 'failed',
                    'api_response' => $apiResponse
                ]);
                throw new \Exception($errorMessage);
            }

        } catch (RequestException $e) {
            $ocrResult->update([
                'status' => 'failed',
                'api_response' => ['error' => $e->getMessage()]
            ]);
            throw $e;
        }
    }

    public function showResult($id)
    {
        $ocrResult = OCRResult::findOrFail($id);
        return view('ocr.result', compact('ocrResult'));
    }

    public function saveText(Request $request, $id)
    {
        $request->validate([
            'edited_text' => 'required|string'
        ]);

        $ocrResult = OCRResult::findOrFail($id);
        $ocrResult->update([
            'edited_text' => $request->edited_text
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Text saved successfully!'
        ]);
    }
}
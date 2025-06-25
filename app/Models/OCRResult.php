<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OCRResult extends Model
{
    use HasFactory;

    protected $table = 'ocr_results';

    protected $fillable = [
        'filename',
        'file_path',
        'extracted_text',
        'edited_text',
        'status',
        'api_response'
    ];

    protected $casts = [
        'api_response' => 'array'
    ];
}
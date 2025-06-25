<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:pdf,png,jpg,jpeg',
                'max:5120' // 5MB
            ]
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.mimes' => 'Only PDF, PNG, JPG, and JPEG files are allowed.',
            'file.max' => 'File size must not exceed 5MB.'
        ];
    }
}
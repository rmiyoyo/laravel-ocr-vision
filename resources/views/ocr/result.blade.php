@extends('layouts.app')

@section('title', 'OCR Result - Laravel OCR Vision')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-file me-2"></i>Uploaded File</h5>
            </div>
            <div class="card-body">
                <h6>{{ $ocrResult->filename }}</h6>
                <p class="text-muted">Status: 
                    <span class="badge bg-{{ $ocrResult->status === 'completed' ? 'success' : ($ocrResult->status === 'failed' ? 'danger' : 'warning') }}">
                        {{ ucfirst($ocrResult->status) }}
                    </span>
                </p>
                @if(Storage::disk('public')->exists($ocrResult->file_path))
                    @if(Str::endsWith($ocrResult->file_path, ['.png', '.jpg', '.jpeg']))
                        <img src="{{ Storage::url($ocrResult->file_path) }}" class="img-fluid rounded" alt="Uploaded Image">
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-pdf fa-5x text-danger"></i>
                            <p class="mt-2">PDF File</p>
                            <a href="{{ Storage::url($ocrResult->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>View PDF
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-text-width me-2"></i>Extracted Text</h5>
                <button id="saveBtn" class="btn btn-light btn-sm">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </div>
            <div class="card-body">
                @if($ocrResult->status === 'completed')
                    <textarea id="textEditor">{{ $ocrResult->edited_text ?: $ocrResult->extracted_text }}</textarea>
                    <div id="saveStatus" class="mt-2" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-check me-1"></i>Text saved successfully!
                        </div>
                    </div>
                @elseif($ocrResult->status === 'failed')
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        OCR processing failed. Please try uploading a different file.
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Processing file...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 text-center">
        <a href="{{ route('ocr.index') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-2"></i>Upload Another File
        </a>
    </div>
</div>
@endsection

@section('scripts')
@if($ocrResult->status === 'completed')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE editor
    tinymce.init({
        selector: '#textEditor',
        height: 400,
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }'
    });

    // Save functionality
    document.getElementById('saveBtn').addEventListener('click', function() {
        const content = tinymce.get('textEditor').getContent({ format: 'text' });
        
        fetch(`{{ route('ocr.save', $ocrResult->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                edited_text: content
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const saveStatus = document.getElementById('saveStatus');
                saveStatus.style.display = 'block';
                setTimeout(() => {
                    saveStatus.style.display = 'none';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save text. Please try again.');
        });
    });
});
</script>
@endif
@endsection
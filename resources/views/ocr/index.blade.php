@extends('layouts.app')

@section('title', 'Upload File - Laravel OCR Vision')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-upload me-2"></i>Upload File for OCR Processing</h4>
            </div>
            <div class="card-body">
                <form id="uploadForm" action="{{ route('ocr.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="upload-area" id="uploadArea">
                        <div class="file-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h5>Drag & Drop your file here</h5>
                        <p class="text-muted">or click to browse</p>
                        <input type="file" id="fileInput" name="file" accept=".pdf,.png,.jpg,.jpeg" style="display: none;" required>
                        <div id="fileInfo" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-file me-2"></i>
                                <span id="fileName"></span>
                                <span id="fileSize" class="text-muted ms-2"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn" disabled>
                            <i class="fas fa-magic me-2"></i>Process with OCR
                        </button>
                    </div>
                </form>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Supported formats: PDF, PNG, JPG, JPEG (Max size: 5MB)
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');

    // Click to upload
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect();
        }
    });

    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            fileInfo.style.display = 'block';
            submitBtn.disabled = false;
        }
    }
});
</script>
@endsection
# Laravel OCR Vision

A Laravel application for Optical Character Recognition (OCR) processing with text editing capabilities. This application allows users to upload documents and images, extracts text using OCR technology, and provides an interface to edit and save the extracted text.

## Features

- **Multi-format Support**: Process PDF, PNG, JPG, and JPEG files
- **Drag & Drop Interface**: User-friendly file upload experience
- **OCR Processing**: Integration with OCR.space API for accurate text extraction
- **Rich Text Editing**: TinyMCE integration for text editing and formatting
- **File Management**: Stores original files and processed results
- **Responsive Design**: Works on desktop and mobile devices
- **Status Tracking**: Monitor processing status of each upload
- **API Integration**: Easy to extend with other OCR services

## Requirements

- PHP 8.0 or higher
- Laravel 9.x or higher
- Composer
- MySQL or other supported database
- OCR.space API key (free tier available)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/laravel-ocr-vision.git
   cd laravel-ocr-vision
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Create and configure the `.env` file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Configure database settings in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_ocr
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Add your OCR.space API key to `.env`:
   ```env
   OCR_SPACE_API_KEY=your_api_key_here
   ```

7. Run migrations:
   ```bash
   php artisan migrate
   ```

8. Create storage link:
   ```bash
   php artisan storage:link
   ```

9. (Optional) Install frontend dependencies:
   ```bash
   npm install
   npm run dev
   ```

10. Start the development server:
    ```bash
    php artisan serve
    ```

## Usage

1. Access the application in your browser (default: http://localhost:8000)
2. Upload a file using the drag & drop interface or file browser
3. Wait for the OCR processing to complete
4. View and edit the extracted text using the rich text editor
5. Save your changes with the "Save Changes" button

## API Integration

The application currently uses the [OCR.space](https://ocr.space/) API for text recognition. You can obtain a free API key from their website.

To configure or change the OCR service:

1. Modify the `OCRService` class in `app/Services/OCRService.php`
2. Update the API endpoint and parameters as needed
3. Add your API key to the `.env` file

## Configuration

Key configuration options in `.env`:

```env
# File upload settings
UPLOAD_MAX_FILESIZE=5120 # in KB (5MB)
ALLOWED_FILE_TYPES=pdf,png,jpg,jpeg

# OCR Settings
OCR_SPACE_API_KEY=your_api_key
OCR_LANGUAGE=eng
OCR_DETECT_ORIENTATION=true
OCR_SCALE=true
```

## Customization

### Views
All frontend views are located in `resources/views/ocr/`. You can modify these to change the application's appearance.

### Processing Logic
The OCR processing logic is contained in `app/Services/OCRService.php`. 

### Validation
File upload validation rules can be modified in `app/Http/Requests/FileUploadRequest.php`.

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a new branch for your feature
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is open-source software licensed under the [MIT license](./LICENSE).

## Credits

- [Laravel](https://laravel.com)
- [OCR.space](https://ocr.space/)
- [TinyMCE](https://www.tiny.cloud/)
- [Bootstrap](https://getbootstrap.com/)
- [Font Awesome](https://fontawesome.com/)

---

For support or questions, please open an issue on the GitHub repository.
<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Media;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UploadFile extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => 'required|max:2048'
        ];
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->file('file');
    }
}

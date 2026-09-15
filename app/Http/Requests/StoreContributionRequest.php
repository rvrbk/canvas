<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContributionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'strokes' => ['required', 'array', 'min:1', 'max:500'],
            'strokes.*.color' => ['required', 'string', 'max:20'],
            'strokes.*.width' => ['required', 'numeric', 'min:1', 'max:50'],
            'strokes.*.points' => ['required', 'array', 'min:2'],
            'strokes.*.points.*.x' => ['required', 'numeric'],
            'strokes.*.points.*.y' => ['required', 'numeric'],
        ];
    }
}

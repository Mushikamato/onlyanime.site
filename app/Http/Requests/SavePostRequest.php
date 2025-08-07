<?php

namespace App\Http\Requests;

use App\Rules\PPVMinMax;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // TEMPORARY DEBUG CODE - ADD THIS LINE:
        \Log::info('SavePostRequest received data:', $this->all());
        
        $minText = (int) getSetting('feed.min_post_description');
        
        return [
            // This rule means 'text' is only required if 'attachments' is not present.
            'text' => 'required_without:attachments|nullable|string|min:' . $minText,
            // This rule means 'attachments' is only required if 'text' is not present.
            'attachments' => 'required_without:text|nullable|array',
            'price' => [new PPVMinMax('post')],
            'is_adult_content' => 'boolean',
            'content_type' => [
                'required',
                Rule::in(['cosplay', 'anime']),
            ],
            'postReleaseDate' => 'nullable|date',
            'postExpireDate' => 'nullable|date|after:postReleaseDate',
            'pollAnswers' => 'nullable|array',
            'pollAnswers.*.text' => 'required_with:pollAnswers|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'content_type.required' => 'Please choose your content type (Anime or Cosplay).',
            'content_type.in' => 'The selected content type is invalid. Please choose either Anime or Cosplay.',
            'text.min' => __('text_min_if_no_media', ['min' => (int) getSetting('feed.min_post_description')]),
            'text.required_without' => __('A post must have either text or attachments.'),
            'attachments.required_without' => __('A post must have either text or attachments.'),
        ];
    }
}
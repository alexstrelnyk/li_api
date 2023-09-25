<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\ContentItem;

use App\Http\Requests\ApiRequest;
use App\Models\ContentItem;
use App\Rules\ContentTypeRule;
use App\Rules\StatusRule;

class CreateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'focus_id' => 'required|exists:focuses,id',
            'topic_id' => 'required_if:content_type,' . ContentItem::TIP_CONTENT_TYPE,
            'status' => ['required', new StatusRule(new ContentItem())],
            'content_type' => ['required', new ContentTypeRule(new ContentItem())],
            'primer_title' => 'required',
            'primer_content' => 'required',
            'reading_time' => 'required',
            'info_content_image' => 'required|url',
            'info_quick_tip' => 'required',
            'info_full_content' => 'required',
            'info_video_uri' => 'required_if:content_type,' . ContentItem::VIDEO_CONTENT_TYPE,
            'info_audio_uri' => 'required_if:content_type,' . ContentItem::AUDIO_CONTENT_TYPE,
            'info_source_title' => 'required',
            'info_source_link' => 'required',
            'has_reflection' => 'required',
            'reflection_help_text' => 'required_if:has_reflection,true',
            'title' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'focus_id.required' => 'Focus with passed ID not found',
        ];
    }


    /**
     * @return int
     */
    public function getFocusId(): int
    {
        return (int) $this->get('focus_id');
    }
}

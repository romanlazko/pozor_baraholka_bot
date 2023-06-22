<?php 

namespace App\Bots\pozor_baraholka_bot\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'city'                  => 'nullable|required|string',
            'type'                  => 'nullable|required|string',
            'images.*'              => 'nullable|sometimes|image|max:2048',
            'title'                 => 'nullable|required|string',
            'caption'               => 'nullable|required|string',
            'cost'                  => 'nullable|sometimes|string', 
            'category'              => 'nullable|required|string',
            'condition'             => 'nullable|sometimes|string',
            'status'                => 'nullable|sometimes|string',
        ];
    }
}

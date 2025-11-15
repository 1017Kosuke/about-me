<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string', 
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id)
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_photo' => [
                'nullable',
                'image', // 画像ファイルであることを検証
                'mimes:jpeg,png,jpg,gif', // 許可する画像のMIMEタイプ
                'max:4096', // 最大ファイルサイズ（KB単位、ここでは2MB）
            ],
        ];
    }
}

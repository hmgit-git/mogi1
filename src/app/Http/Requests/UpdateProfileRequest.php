<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'ユーザー名を入力してください',
            'zip.required' => '郵便番号を入力してください',
            'zip.regex' => '郵便番号はハイフン（-）を含む形式（例：123-4567）で入力してください',
            'address.required' => '住所を入力してください',
            'profile_image.mimes' => '画像形式は jpeg, png, jpg のいずれかにしてください',
            'profile_image.max' => '画像サイズは2MB以内にしてください',

        ];
    }
}

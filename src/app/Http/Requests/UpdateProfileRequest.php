<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 認証済みユーザーのみに制限する場合は true
    }

    public function rules()
    {
        return [
            'username' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'ユーザー名を入力してください',
            'zip.required' => '郵便番号を入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}

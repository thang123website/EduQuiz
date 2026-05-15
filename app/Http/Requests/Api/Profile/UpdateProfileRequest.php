<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'mobile' => ['sometimes', 'nullable', 'string', 'max:20', Rule::unique('users', 'mobile')->ignore($userId)],
            'gender' => ['sometimes', 'nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'dob' => ['sometimes', 'nullable', 'date'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'latitude' => ['sometimes', 'nullable', 'string', 'max:50'],
            'longitude' => ['sometimes', 'nullable', 'string', 'max:50'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'cover_photo' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:4096'],
            'timezone' => ['sometimes', 'nullable', 'string', 'timezone'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'mobile.unique' => 'Số điện thoại này đã được sử dụng bởi tài khoản khác.',
            'mobile.max' => 'Số điện thoại không hợp lệ.',
            'gender.in' => 'Giới tính không hợp lệ (male, female, other).',
            'dob.date' => 'Ngày sinh không đúng định dạng.',
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png, jpg, gif, svg, webp.',
            'avatar.max' => 'Dung lượng ảnh đại diện không được vượt quá 2MB.',
            'cover_photo.image' => 'Ảnh bìa phải là một tệp hình ảnh.',
            'cover_photo.mimes' => 'Ảnh bìa phải có định dạng jpeg, png, jpg, gif, svg, webp.',
            'cover_photo.max' => 'Dung lượng ảnh bìa không được vượt quá 4MB.',
            'timezone.timezone' => 'Múi giờ không hợp lệ (ví dụ: Asia/Ho_Chi_Minh).',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Helper\Helper;
use App\Rules\CheckUnique;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class OrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = 'organizations';
    
        $hasRecords = DB::table($table)->exists();
    
       return [
            'name' => ['required', 'string', 'max:50'],
            'status' => ['required', 'integer'],
            'email' => ['required'],
            'mobile' => ['required', 'digits:10', 'regex:' . config('constant.phoneRegExp')],
            // 'password' => ['required|min:8'],
            'image' => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
           'organization_role_id' => ['nullable', 'integer', Rule::exists('organization_roles', 'id')],
        ];
        
        // if (!$hasRecords) {
        //     $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        // } else {
        //     $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        // }
       
    }
    


    public function filter($organization = null): array
    {
       
        $data = $this->only(['name', 'status', 'email', 'mobile']);
        $data['parent_id'] = $this->organization_parent_id;
        $data['organization_role_id'] = $this->role_id;
    
        // if ($this->filled('password')) {
        //     $data['password']  = Hash::make($this->password);
        // }

        if ($this->hasFile('image')) {
            if ($organization && $organization->image)  Helper::deleteFile($organization->image);
            $data['image']     = Helper::saveFile($this->file('image'));
        }

        return $data;
    }
}

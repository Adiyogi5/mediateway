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

    // public function rules(): array
    // {
    //     $table      = 'organizations';
       
    //     return [
    //         'name'                  => ['required', 'string',  'max:50'],
    //         'status'                => ['required', 'integer'],
    //         'email'                 => ['required', new CheckUnique($table, $organization_id, 'slug')],
    //         'mobile'                => ['required', 'digits:10', new CheckUnique($table, $organization_id, 'slug'), 'regex:' . config('constant.phoneRegExp')],
    //         'password'              => ['nullable', Rule::requiredIf(!$organization_id), 'string', 'min:8', 'confirmed'],
    //         'image'                 => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
    //         'organization_role_id'               => ['integer', 'nullable', Rule::requiredIf($table == 'organizations'), Rule::exists('organization_roles', 'id')],
    //     ];
    // }

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
        if (!$hasRecords) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }
        // return [
        //     'name' => ['required', 'string', 'max:50'],
        //     'status' => ['required', 'integer'],
        //     'email' => ['required', new CheckUnique($table, null, 'slug')],
        //     'mobile' => ['required', 'digits:10', new CheckUnique($table, null, 'slug'), 'regex:' . config('constant.phoneRegExp')],
        //     'password' => ['nullable', Rule::requiredIf(!$hasRecords), 'string', 'min:8', 'confirmed'],
        //     'image' => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        //     'organization_role_id' => ['integer', 'nullable', Rule::requiredIf($table == 'organizations'), Rule::exists('organization_roles', 'id')],
        // ];
    }
    


    public function filter($organization = null): array
    {
        $data = $this->only(['name', 'status', 'email', 'mobile']);
        $data['organization_role_id'] = $this->role_id;

        if ($this->filled('password')) {
            $data['password']  = Hash::make($this->password);
        }

        if ($this->hasFile('image')) {
            if ($organization && $organization->image)  Helper::deleteFile($organization->image);
            $data['image']     = Helper::saveFile($this->file('image'));
        }

        return $data;
    }
}

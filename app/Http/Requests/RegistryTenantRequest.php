<?php

namespace App\Http\Requests;

use App\Models\RegistryTenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistryTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var RegistryTenant|null $existing */
        $existing = $this->route('registry_tenant');
        $id = $existing?->id;

        return [
            'slug' => [
                'required',
                'string',
                'max:64',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(RegistryTenant::class)->ignore($id),
            ],
            'environment' => ['required', Rule::in(['sandbox', 'production'])],
            'database' => ['required', 'string', 'max:128'],
            'db_host' => ['nullable', 'string', 'max:255'],
            'db_username' => ['nullable', 'string', 'max:128'],
            'db_password' => ['nullable', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:128'],
            'settings_json' => ['nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
        ]);
    }
}

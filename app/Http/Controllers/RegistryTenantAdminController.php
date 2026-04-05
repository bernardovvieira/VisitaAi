<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistryTenantRequest;
use App\Models\RegistryTenant;
use App\Support\Tenancy\TenantProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistryTenantAdminController extends Controller
{
    public function index(): View
    {
        $tenants = RegistryTenant::query()->orderBy('slug')->get();

        return view('registry_admin.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('registry_admin.create', [
            'tenant' => new RegistryTenant,
            'canProvision' => (bool) config('tenant_registry.provision_enabled'),
        ]);
    }

    public function store(RegistryTenantRequest $request): RedirectResponse
    {
        if ($request->boolean('provision_database') && config('tenant_registry.provision_enabled')) {
            try {
                app(TenantProvisioner::class)->provisionNewTenant(
                    $this->validatedToAttributes($request),
                    true
                );
            } catch (\Throwable $e) {
                return back()->withInput()->withErrors(['store' => $e->getMessage()]);
            }

            return redirect()->route('registry.admin.index')
                ->with('status', __('Tenant criado: base MySQL, migrações e registo concluídos.'));
        }

        $data = $this->validatedToAttributes($request);

        try {
            RegistryTenant::query()->create($data);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['store' => $e->getMessage()]);
        }

        return redirect()->route('registry.admin.index')
            ->with('status', __('Tenant registado.'));
    }

    public function edit(RegistryTenant $registry_tenant): View
    {
        return view('registry_admin.edit', ['tenant' => $registry_tenant]);
    }

    public function update(RegistryTenantRequest $request, RegistryTenant $registry_tenant): RedirectResponse
    {
        $data = $this->validatedToAttributes($request, true);

        try {
            $registry_tenant->fill($data);
            $registry_tenant->save();
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['update' => $e->getMessage()]);
        }

        return redirect()->route('registry.admin.index')
            ->with('status', __('Tenant atualizado.'));
    }

    public function destroy(Request $request, RegistryTenant $registry_tenant): RedirectResponse
    {
        $registry_tenant->delete();

        return redirect()->route('registry.admin.index')
            ->with('status', __('Tenant removido.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedToAttributes(RegistryTenantRequest $request, bool $isUpdate = false): array
    {
        $data = $request->validated();
        unset($data['provision_database']);
        if (isset($data['database']) && trim((string) $data['database']) === '') {
            unset($data['database']);
        }
        $rawJson = $data['settings_json'] ?? null;
        unset($data['settings_json']);

        if ($rawJson !== null && trim((string) $rawJson) !== '') {
            try {
                $data['settings'] = json_decode((string) $rawJson, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \InvalidArgumentException(__('settings_json deve ser JSON válido.').' '.$e->getMessage());
            }
        } else {
            $data['settings'] = null;
        }

        if (empty($data['db_password'])) {
            unset($data['db_password']);
        }

        return $data;
    }
}

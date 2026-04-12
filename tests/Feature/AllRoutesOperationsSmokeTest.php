<?php

namespace Tests\Feature;

use App\Models\Doenca;
use App\Models\Local;
use App\Models\Morador;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Throwable;

class AllRoutesOperationsSmokeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function todas_as_rotas_nomeadas_e_operacoes_nao_retornam_erro_500(): void
    {
        config(['app.debug' => true]);
        $this->withoutMiddleware(ThrottleRequests::class);

        $ctx = $this->buildContext();

        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn (IlluminateRoute $route) => $route->getName() !== null)
            ->reject(fn (IlluminateRoute $route) => in_array($route->uri(), ['debug-session', 'debug-cookie'], true))
            ->values();

        $this->assertGreaterThan(0, $routes->count(), 'Nenhuma rota nomeada foi encontrada para o smoke test.');

        $failures = [];

        foreach ($routes as $route) {
            $method = $this->preferredMethod($route);
            if ($method === null) {
                continue;
            }

            $name = (string) $route->getName();
            $url = $this->urlForRoute($route, $ctx);
            $request = $this->requestBuilderForRoute($route, $ctx);
            $payload = $this->payloadForRoute($name, $method, $ctx);

            try {
                DB::beginTransaction();
                $response = match ($method) {
                    'GET' => $request->get($url),
                    'POST' => $request->post($url, $payload),
                    'PUT' => $request->put($url, $payload),
                    'PATCH' => $request->patch($url, $payload),
                    'DELETE' => $request->delete($url, $payload),
                    default => null,
                };
                DB::rollBack();

                if ($response === null) {
                    continue;
                }

                $status = $response instanceof TestResponse
                    ? $response->baseResponse->getStatusCode()
                    : $response->getStatusCode();

                if ($status >= 500) {
                    $exception = ($response instanceof TestResponse && $response->exception)
                        ? ' | excecao: '.$response->exception->getMessage()
                        : '';
                    $body = '';
                    if ($response instanceof TestResponse) {
                        $body = trim(substr(strip_tags((string) $response->getContent()), 0, 160));
                    }
                    $bodySuffix = $body !== '' ? ' | body: '.$body : '';
                    $failures[] = sprintf('[%s] %s (%s) retornou %d%s%s', $method, $name, $route->uri(), $status, $exception, $bodySuffix);
                }
            } catch (Throwable $e) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                $failures[] = sprintf('[%s] %s (%s) lançou excecao: %s', $method, $name, $route->uri(), $e->getMessage());
            }
        }

        $this->assertEmpty($failures, "Rotas/operacoes com erro interno:\n".implode("\n", $failures));
    }

    private function buildContext(): array
    {
        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);

        $agenteEndemias = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
            'fk_gestor_id' => $gestor->getKey(),
        ]);

        $agenteSaude = User::factory()->create([
            'use_perfil' => 'agente_saude',
            'use_aprovado' => 1,
            'fk_gestor_id' => $gestor->getKey(),
        ]);

        $userToApprove = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 0,
            'fk_gestor_id' => $gestor->getKey(),
        ]);

        // Primeiro local garante fluxo de local primario para rotas do gestor.
        $localPrimary = Local::factory()->create();
        $localSecondary = Local::factory()->create();

        $morador = Morador::factory()->create([
            'fk_local_id' => $localSecondary->getKey(),
        ]);

        $doenca = Doenca::factory()->create();

        $visitaAgente = Visita::query()->create([
            'fk_usuario_id' => $agenteEndemias->getKey(),
            'fk_local_id' => $localSecondary->getKey(),
            'vis_data' => now()->toDateString(),
            'vis_atividade' => '1',
            'vis_pendencias' => false,
        ]);

        $visitaSaude = Visita::query()->create([
            'fk_usuario_id' => $agenteSaude->getKey(),
            'fk_local_id' => $localSecondary->getKey(),
            'vis_data' => now()->toDateString(),
            'vis_atividade' => '7',
            'vis_pendencias' => false,
        ]);

        return [
            'gestor' => $gestor,
            'agente_endemias' => $agenteEndemias,
            'agente_saude' => $agenteSaude,
            'user_to_approve' => $userToApprove,
            'local_primary' => $localPrimary,
            'local_secondary' => $localSecondary,
            'morador' => $morador,
            'doenca' => $doenca,
            'visita_agente' => $visitaAgente,
            'visita_saude' => $visitaSaude,
        ];
    }

    private function preferredMethod(IlluminateRoute $route): ?string
    {
        foreach ($route->methods() as $method) {
            if (! in_array($method, ['HEAD', 'OPTIONS'], true)) {
                return $method;
            }
        }

        return null;
    }

    private function requestBuilderForRoute(IlluminateRoute $route, array $ctx): TestCase
    {
        $name = (string) $route->getName();
        $middleware = $route->gatherMiddleware();

        $authRequired = collect($middleware)->contains(fn (string $m) => str_starts_with($m, 'auth'));
        if (! $authRequired) {
            return $this;
        }

        if (str_starts_with($name, 'gestor.')) {
            return $this->actingAs($ctx['gestor']);
        }

        if (str_starts_with($name, 'agente.')) {
            return $this->actingAs($ctx['agente_endemias']);
        }

        if (str_starts_with($name, 'saude.')) {
            return $this->actingAs($ctx['agente_saude']);
        }

        return $this->actingAs($ctx['gestor']);
    }

    private function urlForRoute(IlluminateRoute $route, array $ctx): string
    {
        $name = (string) $route->getName();

        if ($name === 'verification.verify') {
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(5),
                [
                    'id' => $ctx['gestor']->getKey(),
                    'hash' => sha1($ctx['gestor']->use_email),
                ]
            );
        }

        return route($name, $this->parametersForRoute($route, $ctx));
    }

    private function parametersForRoute(IlluminateRoute $route, array $ctx): array
    {
        $name = (string) $route->getName();
        $params = [];

        foreach ($route->parameterNames() as $parameterName) {
            $params[$parameterName] = match ($parameterName) {
                'local' => $ctx['local_secondary']->getKey(),
                'morador' => $ctx['morador']->getKey(),
                'doenca' => $ctx['doenca']->getKey(),
                'visita' => str_starts_with($name, 'saude.')
                    ? $ctx['visita_saude']->getKey()
                    : $ctx['visita_agente']->getKey(),
                'user' => $ctx['user_to_approve']->getKey(),
                'token' => 'token-teste',
                'id' => $ctx['gestor']->getKey(),
                'hash' => sha1($ctx['gestor']->use_email),
                default => 1,
            };
        }

        return $params;
    }

    private function payloadForRoute(string $name, string $method, array $ctx): array
    {
        if ($method === 'DELETE') {
            return [];
        }

        if ($name === 'login.store') {
            return [
                'use_email' => $ctx['gestor']->use_email,
                'password' => 'password',
            ];
        }

        if ($name === 'profile.update' || $name === 'user-profile-information.update') {
            return [
                'use_nome' => 'Gestor Cobertura',
                'use_cpf' => '12345678901',
                'use_email' => 'gestor.cobertura@example.com',
                'email' => 'gestor.cobertura@example.com',
                'nome' => 'Gestor Cobertura',
            ];
        }

        if ($name === 'password.update' || $name === 'user-password.update') {
            return [
                'current_password' => 'password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ];
        }

        if ($name === 'password.confirm.store' || $name === 'password.confirmation') {
            return [
                'password' => 'password',
            ];
        }

        if ($name === 'verification.send') {
            return [];
        }

        if ($name === 'agente.visitas.sync.submit' || $name === 'saude.visitas.sync.submit') {
            return [
                'visitas' => [
                    [],
                ],
            ];
        }

        if ($name === 'agente.locais.sync.submit') {
            return [
                'locais' => [],
            ];
        }

        if ($name === 'profile.destroy') {
            return [
                'password' => 'password',
            ];
        }

        if ($name === 'logout') {
            return [];
        }

        return [];
    }
}

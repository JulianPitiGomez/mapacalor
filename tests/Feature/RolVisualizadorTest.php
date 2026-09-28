<?php

namespace Tests\Feature;

use App\Livewire\UserForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RolVisualizadorTest extends TestCase
{
    use RefreshDatabase;

    private function visualizador(array $solapas): User
    {
        return User::factory()->create([
            'rol' => User::ROL_VISUALIZADOR,
            'es_supervisor' => false,
            'solapas' => $solapas,
        ]);
    }

    public function test_el_visualizador_entra_a_las_solapas_habilitadas(): void
    {
        $user = $this->visualizador(['estadisticas-actas', 'hechos']);

        $this->actingAs($user)->get(route('estadisticas-actas.index'))->assertOk();
        $this->actingAs($user)->get(route('hechos.index'))->assertOk();
    }

    public function test_el_visualizador_no_entra_a_las_solapas_no_habilitadas(): void
    {
        $user = $this->visualizador(['estadisticas-actas']);

        $this->actingAs($user)->get(route('hechos.index'))->assertForbidden();
        $this->actingAs($user)->get(route('operativos.index'))->assertForbidden();
        $this->actingAs($user)->get(route('estadisticas'))->assertForbidden();
        $this->actingAs($user)->get(route('usuarios.index'))->assertForbidden();
    }

    public function test_el_visualizador_no_puede_crear_ni_editar(): void
    {
        $user = $this->visualizador(['hechos', 'barrios', 'categorias']);

        $this->actingAs($user)->get(route('hechos.create'))->assertForbidden();
        $this->actingAs($user)->get(route('barrios.create'))->assertForbidden();
        $this->actingAs($user)->post(route('barrios.store'), ['nombre' => 'Centro'])->assertForbidden();
        $this->actingAs($user)->post(route('subcategorias.store'), ['nombre' => 'X'])->assertForbidden();

        $this->assertDatabaseMissing('barrios', ['nombre' => 'Centro']);
    }

    public function test_el_visualizador_sigue_pudiendo_editar_su_perfil(): void
    {
        $user = $this->visualizador(['hechos']);

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
        $this->actingAs($user)
            ->patch(route('profile.update'), ['name' => 'Nuevo', 'email' => $user->email])
            ->assertRedirect();
    }

    public function test_el_dashboard_manda_a_la_primera_solapa_habilitada(): void
    {
        $user = $this->visualizador(['estadisticas-actas']);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertRedirect(route('estadisticas-actas.index'));
    }

    public function test_el_usuario_normal_conserva_sus_permisos(): void
    {
        $user = User::factory()->create(['rol' => User::ROL_NORMAL, 'es_supervisor' => false]);

        $this->actingAs($user)->get(route('hechos.index'))->assertOk();
        $this->actingAs($user)->get(route('hechos.create'))->assertOk();
        $this->actingAs($user)->get(route('operativos.index'))->assertForbidden();
        $this->actingAs($user)->get(route('usuarios.index'))->assertForbidden();
    }

    public function test_el_supervisor_ve_todo(): void
    {
        $user = User::factory()->create(['rol' => User::ROL_SUPERVISOR, 'es_supervisor' => true]);

        $this->actingAs($user)->get(route('usuarios.index'))->assertOk();
        $this->actingAs($user)->get(route('grupos.index'))->assertOk();
        $this->assertTrue($user->puedeEditar());
        $this->assertTrue($user->puedeVer('estadisticas-actas'));
    }

    public function test_la_solapa_de_usuarios_no_es_asignable_a_un_visualizador(): void
    {
        $this->assertArrayNotHasKey('usuarios', User::SOLAPAS);

        $user = $this->visualizador(['hechos', 'usuarios']);

        $this->assertSame(['hechos'], $user->solapasVisibles());
        $this->actingAs($user)->get(route('usuarios.index'))->assertForbidden();
    }

    public function test_al_visualizador_no_se_le_muestran_los_botones_de_alta(): void
    {
        $user = $this->visualizador(['hechos']);

        $this->actingAs($user)->get(route('hechos.index'))
            ->assertOk()
            ->assertDontSee('Nuevo Hecho')
            ->assertDontSee(route('hechos.create'));
    }

    public function test_el_formulario_guarda_el_rol_y_las_solapas(): void
    {
        $supervisor = User::factory()->create(['rol' => User::ROL_SUPERVISOR, 'es_supervisor' => true]);

        Livewire::actingAs($supervisor)
            ->test(UserForm::class)
            ->set('name', 'Consulta')
            ->set('email', 'consulta@mercedes.gob.ar')
            ->set('password', 'secreto123')
            ->set('password_confirmation', 'secreto123')
            ->set('rol', User::ROL_VISUALIZADOR)
            ->set('solapas', ['estadisticas', 'estadisticas-actas'])
            ->call('save')
            ->assertHasNoErrors();

        $nuevo = User::where('email', 'consulta@mercedes.gob.ar')->firstOrFail();

        $this->assertSame(User::ROL_VISUALIZADOR, $nuevo->rol);
        $this->assertFalse($nuevo->es_supervisor);
        $this->assertSame(['estadisticas', 'estadisticas-actas'], $nuevo->solapas);
    }

    public function test_un_visualizador_sin_solapas_no_se_puede_guardar(): void
    {
        $supervisor = User::factory()->create(['rol' => User::ROL_SUPERVISOR, 'es_supervisor' => true]);

        Livewire::actingAs($supervisor)
            ->test(UserForm::class)
            ->set('name', 'Consulta')
            ->set('email', 'consulta2@mercedes.gob.ar')
            ->set('password', 'secreto123')
            ->set('password_confirmation', 'secreto123')
            ->set('rol', User::ROL_VISUALIZADOR)
            ->call('save')
            ->assertHasErrors(['solapas']);
    }

    public function test_al_pasar_de_visualizador_a_supervisor_se_limpian_las_solapas(): void
    {
        $supervisor = User::factory()->create(['rol' => User::ROL_SUPERVISOR, 'es_supervisor' => true]);
        $otro = $this->visualizador(['hechos']);

        Livewire::actingAs($supervisor)
            ->test(UserForm::class, ['userId' => $otro->id])
            ->set('rol', User::ROL_SUPERVISOR)
            ->call('save')
            ->assertHasNoErrors();

        $otro->refresh();

        $this->assertSame(User::ROL_SUPERVISOR, $otro->rol);
        $this->assertTrue($otro->es_supervisor);
        $this->assertNull($otro->solapas);
    }

    public function test_un_visualizador_no_puede_guardar_desde_un_componente_livewire(): void
    {
        $user = $this->visualizador(['hechos']);

        Livewire::actingAs($user)
            ->test(UserForm::class)
            ->set('name', 'Intruso')
            ->set('email', 'intruso@mercedes.gob.ar')
            ->set('password', 'secreto123')
            ->set('password_confirmation', 'secreto123')
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'intruso@mercedes.gob.ar']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\TopUnitKerja;
use App\Models\User;
use App\Models\VdptMonitoring;
use App\Models\VdsCategorie;
use App\Models\VdsLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RiskCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private TopUnitKerja $unit;

    private VdsCategorie $category;

    private VdsLevel $level;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['risk.view', 'risk.create', 'risk.edit', 'risk.delete'] as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->adminUser = User::factory()->create();
        $this->adminUser->givePermissionTo(['risk.view', 'risk.create', 'risk.edit', 'risk.delete']);

        $this->unit = TopUnitKerja::create(['nama_unit' => 'IT']);
        $this->category = VdsCategorie::create(['category_name' => 'Operasional']);
        $this->level = VdsLevel::create(['level_name' => 'High']);
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'id_unit' => $this->unit->id_unit,
            'id_category' => $this->category->id_category,
            'id_level' => $this->level->id_level,
            'risk_event_deta' => 'Server down menyebabkan layanan tidak tersedia',
            'value' => 16,
            'inherent' => 20,
            'trend' => 'Naik',
            'type' => 'Proyek',
            'status' => 1,
        ];
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────

    public function test_index_page_is_accessible_with_permission(): void
    {
        VdptMonitoring::create($this->validPayload());

        $response = $this->actingAs($this->adminUser)->get(route('risks.index'));

        $response->assertOk();
        $response->assertViewIs('risks.index');
        $response->assertViewHas('risks');
    }

    public function test_index_page_is_forbidden_without_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('risks.index'));

        $response->assertForbidden();
    }

    public function test_index_can_filter_by_search(): void
    {
        VdptMonitoring::create(array_merge($this->validPayload(), ['risk_event_deta' => 'Server Down Incident']));
        VdptMonitoring::create(array_merge($this->validPayload(), ['risk_event_deta' => 'Data Breach Keuangan']));

        $response = $this->actingAs($this->adminUser)
            ->get(route('risks.index', ['search' => 'Server']));

        $response->assertOk();
        $response->assertSee('Server Down Incident');
        $response->assertDontSee('Data Breach Keuangan');
    }

    public function test_index_can_filter_by_type(): void
    {
        VdptMonitoring::create(array_merge($this->validPayload(), ['risk_event_deta' => 'Risiko Proyek', 'type' => 'Proyek']));
        VdptMonitoring::create(array_merge($this->validPayload(), ['risk_event_deta' => 'Risiko Non Proyek', 'type' => 'Non-Proyek']));

        $response = $this->actingAs($this->adminUser)
            ->get(route('risks.index', ['type' => 'Proyek']));

        $response->assertOk();
        $response->assertSee('Risiko Proyek');
        $response->assertDontSee('Risiko Non Proyek');
    }

    public function test_index_can_filter_by_status(): void
    {
        VdptMonitoring::create(array_merge($this->validPayload(), ['risk_event_deta' => 'Risiko Aktif', 'status' => true]));
        VdptMonitoring::create(array_merge($this->validPayload(), ['risk_event_deta' => 'Risiko Nonaktif', 'status' => false]));

        $response = $this->actingAs($this->adminUser)
            ->get(route('risks.index', ['status' => '1']));

        $response->assertOk();
        $response->assertSee('Risiko Aktif');
        $response->assertDontSee('Risiko Nonaktif');
    }

    // ── CREATE ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible_with_permission(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('risks.create'));

        $response->assertOk();
        $response->assertViewIs('risks.create');
    }

    public function test_create_page_is_forbidden_without_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('risks.create'));

        $response->assertForbidden();
    }

    // ── STORE ─────────────────────────────────────────────────────────────────

    public function test_risk_can_be_created(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('risks.store'), $this->validPayload());

        $response->assertRedirect(route('risks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vdpt_monitoring', [
            'id_unit' => $this->unit->id_unit,
            'risk_event_deta' => 'Server down menyebabkan layanan tidak tersedia',
            'value' => 16,
            'inherent' => 20,
            'trend' => 'Naik',
            'type' => 'Proyek',
            'status' => 1,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('risks.store'), []);

        $response->assertSessionHasErrors(['id_unit', 'id_category', 'id_level', 'risk_event_deta', 'value', 'inherent', 'trend', 'type', 'status']);
    }

    public function test_store_validates_trend_enum(): void
    {
        $payload = array_merge($this->validPayload(), ['trend' => 'InvalidTrend']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('risks.store'), $payload);

        $response->assertSessionHasErrors(['trend']);
    }

    public function test_store_validates_type_enum(): void
    {
        $payload = array_merge($this->validPayload(), ['type' => 'InvalidType']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('risks.store'), $payload);

        $response->assertSessionHasErrors(['type']);
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible_with_permission(): void
    {
        $risk = VdptMonitoring::create($this->validPayload());

        $response = $this->actingAs($this->adminUser)
            ->get(route('risks.edit', $risk->id_monitoring));

        $response->assertOk();
        $response->assertViewIs('risks.edit');
    }

    public function test_edit_page_is_forbidden_without_permission(): void
    {
        $risk = VdptMonitoring::create($this->validPayload());
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('risks.edit', $risk->id_monitoring));

        $response->assertForbidden();
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────

    public function test_risk_can_be_updated(): void
    {
        $risk = VdptMonitoring::create($this->validPayload());

        $updatedPayload = array_merge($this->validPayload(), [
            'risk_event_deta' => 'Deskripsi risiko diperbarui',
            'value' => 10,
            'trend' => 'Turun',
            'type' => 'Non-Proyek',
            'status' => 0,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('risks.update', $risk->id_monitoring), $updatedPayload);

        $response->assertRedirect(route('risks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vdpt_monitoring', [
            'id_monitoring' => $risk->id_monitoring,
            'risk_event_deta' => 'Deskripsi risiko diperbarui',
            'value' => 10,
            'trend' => 'Turun',
            'type' => 'Non-Proyek',
            'status' => 0,
        ]);
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────

    public function test_risk_can_be_deleted(): void
    {
        $risk = VdptMonitoring::create($this->validPayload());

        $response = $this->actingAs($this->adminUser)
            ->delete(route('risks.destroy', $risk->id_monitoring));

        $response->assertRedirect(route('risks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('vdpt_monitoring', ['id_monitoring' => $risk->id_monitoring]);
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $risk = VdptMonitoring::create($this->validPayload());
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('risks.destroy', $risk->id_monitoring));

        $response->assertForbidden();
        $this->assertDatabaseHas('vdpt_monitoring', ['id_monitoring' => $risk->id_monitoring]);
    }
}

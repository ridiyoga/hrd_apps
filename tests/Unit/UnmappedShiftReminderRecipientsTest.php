<?php

use App\Models\Approval;
use App\Models\ApprovalGroup;
use App\Models\ApprovalUser;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\LevelJabatan;
use App\Models\StrukturModel;
use App\Services\UnmappedShiftReminderRecipients;

function reminderPerson(string $id, int $level, string $section = 'employee-section'): Karyawan
{
    $position = (new KaryawanJabatan)->forceFill([
        'jabatan_id' => $id.'-position', 'karyawan_id' => $id,
        'holding_id' => 'holding-a', 'departemen_id' => 'production',
        'divisi_id' => 'production', 'bagian_id' => $section,
        'main' => 1, 'is_active' => 1,
    ])->setRelation('Jabatan', (new Jabatan)->setRelation(
        'LevelJabatan', (new LevelJabatan)->forceFill(['level_jabatan' => $level])
    ));

    $person = (new Karyawan)->forceFill([
        'id' => $id, 'name' => $id, 'email' => $id.'@example.com', 'status_aktif' => 'AKTIF',
    ])->setRelation('KaryawanJabatanOne', $position);
    $position->setRelation('Karyawan', $person);

    return $person;
}

function reminderNode(Karyawan $person, ?string $parent): StrukturModel
{
    $position = $person->KaryawanJabatanOne;

    return (new StrukturModel)->forceFill([
        'id' => $person->id, 'holding' => $position->holding_id,
        'jabatan' => $position->jabatan_id, 'parent' => $parent,
    ])->setRelation('Jabatan', $position->Jabatan)
        ->setRelation('UserKaryawan', collect([$position]));
}

function reminderScenario(): array
{
    $employee = reminderPerson('employee', 6);
    $coordinator = reminderPerson('coordinator', 5);
    $supervisor = reminderPerson('supervisor', 4, 'combined-sections');
    $manager = reminderPerson('manager', 2);
    $unrelated = reminderPerson('unrelated-supervisor', 4);
    $structure = collect([
        reminderNode($employee, 'coordinator'), reminderNode($coordinator, 'supervisor'),
        reminderNode($supervisor, 'manager'), reminderNode($manager, null),
        reminderNode($unrelated, 'manager'),
    ])->keyBy('id');
    $approval = (new Approval)->forceFill([
        'holding_id' => 'holding-a', 'departemen_id' => ['production'],
    ])->setRelation('levelJabatan', (new LevelJabatan)->forceFill(['level_jabatan' => 4]))
        ->setRelation('ApprovalUser', collect([$coordinator, $supervisor, $manager, $unrelated])->map(
            fn ($person) => (new ApprovalUser)->forceFill(['karyawan_id' => $person->id])
        ));
    $group = (new ApprovalGroup)->forceFill(['holding_id' => 'holding-a'])
        ->setRelation('approval', collect([$approval]));

    return [$employee, collect([$group]), $structure];
}

function resolveReminder(array $scenario): array
{
    return (new UnmappedShiftReminderRecipients)->resolve(...$scenario);
}

test('follows the coordinator to the authorized level 4 supervisor across different sections', function () {
    $result = resolveReminder(reminderScenario());

    expect($result['recipients']->pluck('id')->all())->toBe(['supervisor'])
        ->and($result['reasons'])->toBeEmpty();
});

test('does not substitute another registered supervisor when the responsible supervisor is unauthorized', function () {
    $scenario = reminderScenario();
    $approval = $scenario[1]->first()->approval->first();
    $approval->setRelation('ApprovalUser', $approval->ApprovalUser->reject(fn ($a) => $a->karyawan_id === 'supervisor'));
    $result = resolveReminder($scenario);

    expect($result['recipients'])->toBeEmpty()
        ->and($result['reasons']->join(' '))->toContain('supervisor: belum terdaftar');
});

test('requires approval in the matching holding department and level', function ($invalidScope) {
    $scenario = reminderScenario();
    $group = $scenario[1]->first();
    $approval = $group->approval->first();
    match ($invalidScope) {
        'group holding' => $group->holding_id = 'holding-b',
        'approval holding' => $approval->holding_id = 'holding-b',
        'department' => $approval->departemen_id = ['other-department'],
        'level' => $approval->levelJabatan->level_jabatan = 2,
    };
    $result = resolveReminder($scenario);

    expect($result['recipients'])->toBeEmpty()
        ->and($result['reasons']->join(' '))->toContain('approval Mapping Shift');
})->with(['group holding', 'approval holding', 'department', 'level']);

test('does not send to inactive or invalid supervisor assignments', function ($invalidState) {
    $scenario = reminderScenario();
    $assignment = $scenario[2]['supervisor']->UserKaryawan->first();
    match ($invalidState) {
        'other holding' => $assignment->holding_id = 'holding-b',
        'inactive position' => $assignment->is_active = 0,
        'wrong position' => $assignment->jabatan_id = 'another-position',
        'inactive employee' => $assignment->Karyawan->status_aktif = 'NON AKTIF',
        'missing employee' => $assignment->setRelation('Karyawan', null),
        'invalid email' => $assignment->Karyawan->email = 'invalid',
    };
    $result = resolveReminder($scenario);

    expect($result['recipients'])->toBeEmpty()->and($result['reasons'])->not->toBeEmpty();
    if ($invalidState === 'invalid email') {
        expect($result['reasons']->join(' '))->toContain('email kosong atau tidak valid');
    }
})->with(['other holding', 'inactive position', 'wrong position', 'inactive employee', 'missing employee', 'invalid email']);

test('stops at a vacant supervisor instead of escalating to another supervisor or manager', function () {
    $scenario = reminderScenario();
    $scenario[2]['supervisor']->setRelation('UserKaryawan', collect());
    $scenario[2]['supervisor']->parent = 'unrelated-supervisor';
    $result = resolveReminder($scenario);

    expect($result['recipients'])->toBeEmpty()
        ->and($result['reasons']->join(' '))->toContain('belum memiliki karyawan aktif');
});

test('safely rejects missing cyclic cross-holding and incomplete structure paths', function ($fault) {
    $scenario = reminderScenario();
    match ($fault) {
        'missing employee node' => $scenario[2]->forget('employee'),
        'broken parent' => $scenario[2]['coordinator']->parent = 'missing',
        'cycle' => $scenario[2]['coordinator']->parent = 'employee',
        'other holding' => $scenario[2]['supervisor']->holding = 'holding-b',
        'missing level' => $scenario[2]['supervisor']->Jabatan->setRelation('LevelJabatan', null),
        'no supervisor' => $scenario[2]['coordinator']->parent = 'manager',
    };
    $result = resolveReminder($scenario);

    expect($result['recipients'])->toBeEmpty()->and($result['reasons'])->not->toBeEmpty();
})->with(['missing employee node', 'broken parent', 'cycle', 'other holding', 'missing level', 'no supervisor']);

test('keeps all authorized holders of the responsible supervisor position without duplicates', function () {
    $scenario = reminderScenario();
    $node = $scenario[2]['supervisor'];
    $secondary = reminderPerson('secondary-supervisor', 4, 'combined-sections')->KaryawanJabatanOne;
    $secondary->jabatan_id = $node->jabatan;
    $secondary->main = 2;
    $node->UserKaryawan->push($secondary, $secondary);
    $scenario[1]->first()->approval->first()->ApprovalUser->push(
        (new ApprovalUser)->forceFill(['karyawan_id' => 'secondary-supervisor'])
    );
    $result = resolveReminder($scenario);

    expect($result['recipients']->pluck('id')->all())->toBe(['supervisor', 'secondary-supervisor']);
});

test('does not notify self peers or manager for employees at supervisor rank or above', function ($level) {
    $scenario = reminderScenario();
    $scenario[0]->KaryawanJabatanOne->Jabatan->LevelJabatan->level_jabatan = $level;
    $result = resolveReminder($scenario);

    expect($result['recipients'])->toBeEmpty()->and($result['reasons'])->not->toBeEmpty();
})->with([4, 3, 2, 1, 0, -1]);

test('explains missing approval and missing employee position data', function () {
    $scenario = reminderScenario();
    $scenario[1] = collect();
    $result = resolveReminder($scenario);
    expect($result['recipients'])->toBeEmpty()
        ->and($result['reasons']->join(' '))->toContain('approval Mapping Shift');

    $scenario[0]->setRelation('KaryawanJabatanOne', null);
    $result = resolveReminder($scenario);
    expect($result['recipients'])->toBeEmpty()
        ->and($result['reasons']->join(' '))->toContain('belum lengkap');
});

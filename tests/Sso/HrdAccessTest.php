<?php
namespace Tests\Sso;
use App\Models\User;
use App\Services\KeycloakClient;
use App\Services\PortalAccess;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
class HrdAccessTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        // Konfigurasi aplikasi HRD memakai pgsql secara eksplisit: ganti koneksi itu ke memory untuk test saja.
        config(['database.default'=>'pgsql','database.connections.pgsql'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>''],
            'sso.enabled'=>true,'sso.issuer'=>'http://192.168.3.240:8080/realms/internal','session.driver'=>'array']);
        DB::purge('pgsql');
        Schema::create('users',function(Blueprint $t){$t->string('id')->primary();$t->string('username');$t->string('is_admin');$t->string('user_aktif');$t->string('karyawan_id')->nullable();});
        Schema::create('external_identities',function(Blueprint $t){$t->id();$t->string('user_id');$t->string('issuer');$t->string('subject');});
        DB::table('users')->insert(['id'=>'hrd-test-user','username'=>'superadmin3','is_admin'=>'admin','user_aktif'=>'AKTIF']);
        DB::table('external_identities')->insert(['user_id'=>'hrd-test-user','issuer'=>config('sso.issuer'),'subject'=>'test-subject']);
        $this->mock(KeycloakClient::class,fn($m)=>$m->shouldReceive('callback')->andReturn(['claims'=>['sub'=>'test-subject'],'access_token'=>'test-token','id_token'=>'test-id']));
    }
    protected function tearDown():void{DB::purge('pgsql');parent::tearDown();}
    public function test_linked_active_user_enters_hrd_without_changing_role():void{
        $this->mock(PortalAccess::class,fn($m)=>$m->shouldReceive('allows')->once()->with('test-subject')->andReturn(true));
        $this->get('/auth/callback')->assertRedirect('/')->assertSessionHas('sso.subject','test-subject');
        $this->assertAuthenticatedAs(User::find('hrd-test-user'));
        $this->assertSame('admin',User::find('hrd-test-user')->is_admin);
    }
    public function test_portal_denial_does_not_create_hrd_session():void{
        $this->mock(PortalAccess::class,fn($m)=>$m->shouldReceive('allows')->once()->andReturn(false));
        $this->get('/auth/callback')->assertForbidden();$this->assertGuest();
    }
    public function test_disabled_hrd_account_is_rejected():void{
        DB::table('users')->update(['user_aktif'=>'NON AKTIF']);
        $this->get('/auth/callback')->assertForbidden();$this->assertGuest();
    }
    public function test_outage_fails_closed():void{
        $this->mock(PortalAccess::class,fn($m)=>$m->shouldReceive('allows')->once()->andThrow(new \RuntimeException('offline')));
        $this->get('/auth/callback')->assertStatus(503);$this->assertGuest();
    }
}
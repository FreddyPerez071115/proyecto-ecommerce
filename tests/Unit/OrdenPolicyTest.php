<?php

namespace Tests\Unit;

use App\Models\Usuario;
use App\Policies\OrdenPolicy;
use PHPUnit\Framework\TestCase;

class OrdenPolicyTest extends TestCase
{
    public function test_admin_can_view_any_order()
    {
        $policy = new OrdenPolicy();
        $admin = new Usuario(['role' => 'administrador']);

        $this->assertTrue($policy->viewAny($admin));
    }

    public function test_client_cannot_view_any_order()
    {
        $policy = new OrdenPolicy();
        $client = new Usuario(['role' => 'cliente']);

        $this->assertFalse($policy->viewAny($client));
    }
}

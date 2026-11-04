<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** Halaman utama mengarahkan ke dashboard (yang lalu meminta login). */
    public function test_halaman_utama_diarahkan_ke_dashboard(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
    }
}

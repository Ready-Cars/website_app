<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\ServiceType;
use PHPUnit\Framework\TestCase;

class ServiceTypeModelTest extends TestCase
{
    public function test_fillable_and_casts_do_not_include_default_price(): void
    {
        $model = new ServiceType;

        // Fillable should not contain 'default_price'
        $this->assertNotContains('default_price', $model->getFillable());

        // Casts should not contain 'default_price'
        $casts = method_exists($model, 'getCasts') ? $model->getCasts() : [];
        $this->assertArrayNotHasKey('default_price', $casts);

        // Ensure expected fields still present
        $this->assertContains('name', $model->getFillable());
        $this->assertContains('pricing_type', $model->getFillable());
        $this->assertContains('is_active', $model->getFillable());
        $this->assertSame('boolean', $casts['is_active'] ?? null);
    }
}

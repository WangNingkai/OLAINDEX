<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testBasicTest(): void
    {
        // 根路由需要账号配置，此处验证应用可正常响应
        $response = $this->get('/');

        // 未配置账号时返回 404 是预期行为
        $response->assertStatus(404);
    }
}

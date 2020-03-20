<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $ret = [];
        $b = '༓哥⃣哥⃣我⃣没⃣有⃣金⃣币⃣回⃣复⃣不⃣了⃣了⃣呢⃣༓$丫⚬̐⚬̐𝟟𝟜𝟜這͟个͟是͟我͟得͟葳͟星͟+我不༲明༲白༲得༲话༲照༲片༲墙༲也༲有༲得༲"abvq';
        for ($i = 0; $i < strlen($b); $i++) {
            if ($i % 3 != 0) {
                continue;
            }
            $m = 3 * ($i / 3);
            $char = mb_strcut($b, $m, 3, 'utf-8');
            if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $char) || preg_match('/[a-zA-Z1-9_]/', $char)) {
                $ret[] = $char;
            }
        }
        $this->assertTrue(true);
    }
}

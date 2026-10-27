<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ClassDirectiveTest extends TestCase
{
    use VerifiesOutputTrait;

    public function testSimpleClassDirective()
    {
        $this->assertSame(
            'class="bg-reg border shadow"',
            $this->renderBlade('@class(["bg-reg", "border", "shadow"])')
        );
    }


    public function testConditionalClasses(){
        $this->assertSame(
            'class="bg-red"',
            $this->renderBlade('@class(["bg-red", "text-red" => false])')
        );

        $this->assertSame(
            'class="bg-white text-red"',
            $this->renderBlade('@class(["bg-white", "text-red" => true])')
        );
    }

    public function testEscapesOutput(): void
    {
        $this->assertSame(
            'class="bg-red md:bg-blue &quot; onclick=&quot;alert(&#039;hi&#039;)"',
            $this->renderBlade('@class(["bg-red md:bg-blue \" onclick=\"alert(\'hi\')"])')
        );
    }


    public function testEscapesAttributeInjection(): void
    {
        $payload = 'bg-red" onclick="alert(\'xss\')"';

        $this->assertSame(
            'class="bg-red&quot; onclick=&quot;alert(&#039;xss&#039;)&quot;"',
            $this->renderBlade("@class(['$payload'])")
        );
    }

    public function testEscapesTagBreakout(): void
    {
        $payload = '"> <script>alert(1)</script>';

        $this->assertSame(
            'class="&quot;&gt; &lt;script&gt;alert(1)&lt;/script&gt;"',
            $this->renderBlade("@class(['$payload'])")
        );
    }

    public function testEscapesMaliciousArrayKeys(): void
    {
        $payload = '"><img src=x onerror=alert(1)>';

        $this->assertSame(
            'class="&quot;&gt;&lt;img src=x onerror=alert(1)&gt;"',
            $this->renderBlade("@class(['$payload' => true])")
        );
    }

    public function testEscapesMixedContent(): void
    {
        $this->assertSame(
            'class="btn-primary &quot; onmouseover=&quot;alert(1)"',
            $this->renderBlade("@class(['btn-primary' => true, '\" onmouseover=\"alert(1)' => true])")
        );
    }

    public function testEscapesSingleQuoteBreakout(): void
    {
        $payload = "bg-red' onmouseover='alert(1)'";

        $this->assertSame(
            'class="bg-red&#039; onmouseover=&#039;alert(1)&#039;"',
            $this->renderBlade("@class(['$payload'])")
        );
    }

    public function testHandlesNonStringKeys(): void
    {
        $this->assertSame(
            'class="123 456"',
            $this->renderBlade("@class([123 => true, 456 => true])")
        );
    }
}

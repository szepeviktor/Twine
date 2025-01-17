<?php

namespace PHLAK\Twine\Tests\Methods;

use PHLAK\Twine;
use PHLAK\Twine\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Twine\Str::class)]
class HexTest extends TestCase
{
    #[Test]
    public function it_can_be_hex_encoded(): Str
    {
        $string = new Twine\Str('john pinkerton');

        $hex = $string->hex();

        $this->assertInstanceOf(Twine\Str::class, $hex);
        $this->assertEquals('\x6a\x6f\x68\x6e\x20\x70\x69\x6e\x6b\x65\x72\x74\x6f\x6e', $hex);

        return $hex;
    }

    #[Test, Depends('it_can_be_hex_encoded')]
    public function test_it_can_be_hex_decoded(Twine\Str $hex): void
    {
        $plaintext = $hex->hex(Twine\Config\Hex::DECODE);

        $this->assertInstanceOf(Twine\Str::class, $plaintext);
        $this->assertEquals('john pinkerton', $plaintext);
    }

    #[Test]
    public function a_multibyte_string_can_be_hex_encoded(): Str
    {
        $string = new Twine\Str('宮本 茂');

        $hex = $string->hex();

        $this->assertInstanceOf(Twine\Str::class, $hex);
        $this->assertEquals('\x5bae\x672c\x20\x8302', $hex);

        return $hex;
    }

    #[Test, Depends('a_multibyte_string_can_be_hex_encoded')]
    public function a_multibyte_string_can_be_hex_decoded(Twine\Str $hex): void
    {
        $plaintext = $hex->hex(Twine\Config\Hex::DECODE);

        $this->assertInstanceOf(Twine\Str::class, $plaintext);
        $this->assertEquals('宮本 茂', $plaintext);
    }
}

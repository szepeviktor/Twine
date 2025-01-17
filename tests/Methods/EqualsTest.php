<?php

namespace PHLAK\Twine\Tests\Methods;

use PHLAK\Twine;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Twine\Str::class)]
class EqualsTest extends TestCase
{
    #[Test]
    public function it_can_determine_if_it_equals_another_string_exactly(): void
    {
        $string = new Twine\Str('john pinkerton');

        $matches = $string->equals('john pinkerton');
        $differs = $string->equals('JoHN PiNKeRToN');

        $this->assertTrue($matches);
        $this->assertFalse($differs);
    }

    #[Test]
    public function it_can_determine_if_it_equals_another_string_ignoring_case(): void
    {
        $string = new Twine\Str('john pinkerton');

        $matches = $string->equals('JoHN PiNKeRToN', Twine\Config\Equals::CASE_INSENSITIVE);
        $differs = $string->equals('BoB BeLCHeR', Twine\Config\Equals::CASE_INSENSITIVE);

        $this->assertTrue($matches);
        $this->assertFalse($differs);
    }

    #[Test]
    public function it_can_determine_if_it_equals_another_instance_of_itself(): void
    {
        $string1 = new Twine\Str('john pinkerton');
        $string2 = new Twine\Str('JoHN PiNKeRToN');
        $string3 = new Twine\Str('BoB BeLCHeR');

        $this->assertTrue($string1->equals(clone $string1));
        $this->assertFalse($string1->equals($string2));
        $this->assertTrue($string1->equals($string2, Twine\Config\Equals::CASE_INSENSITIVE));
        $this->assertFalse($string1->equals($string3, Twine\Config\Equals::CASE_INSENSITIVE));
    }

    #[Test]
    public function it_can_determine_if_a_multibyte_string_equals_another_multibyte_string_exactly(): void
    {
        $string = new Twine\Str('宮本 茂');

        $matches = $string->equals('宮本 茂');
        $differs = $string->equals('任天堂');

        $this->assertTrue($matches);
        $this->assertFalse($differs);
    }
}

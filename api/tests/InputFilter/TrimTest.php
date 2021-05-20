<?php

namespace App\Tests\InputFilter;

use App\InputFilter\Trim;
use App\InputFilter\UnexpectedValueException;
use PHPUnit\Framework\TestCase;

class TrimTest extends TestCase {
    /**
     * @dataProvider getExamples
     */
    public function testInputFiltering($input, $output) {
        // given
        $data = ['key' => $input];
        $outputData = ['key' => $output];
        $trim = new Trim();

        // when
        $result = $trim->applyTo($data, 'key');

        // then
        $this->assertEquals($outputData, $result);
    }

    public function getExamples() {
        return [
          ['', ''],
          ['abc', 'abc'],
          [' abc', 'abc'],
          ['abc def', 'abc def'],
          ['abc ', 'abc'],
          ["\tabc", 'abc'],
          ['  abc', 'abc'],
          ["\t abc ", 'abc'],
        ];
    }

    public function testDoesNothingWhenKeyIsMissing() {
        // given
        $data = ['otherkey' => 'something'];
        $trim = new Trim();

        // when
        $result = $trim->applyTo($data, 'key');

        // then
        $this->assertEquals($data, $result);
    }

    public function testDoesNothingWhenValueIsNull() {
        // given
        $data = ['key' => null];
        $trim = new Trim();

        // when
        $result = $trim->applyTo($data, 'key');

        // then
        $this->assertEquals($data, $result);
    }

    public function testThrowsWhenValueIsNotStringable() {
        // given
        $data = ['key' => new \stdClass()];
        $trim = new Trim();

        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $result = $trim->applyTo($data, 'key');
    }
}
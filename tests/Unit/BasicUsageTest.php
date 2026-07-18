<?php

use JordJD\HCLParser\HCLParser;
use JordJD\HCLParser\Exceptions\HCLParseException;
use PHPUnit\Framework\TestCase;

class BasicUsageTest extends TestCase
{
    public function testBasicParsing()
    {
        $hcl = file_get_contents(__DIR__.'/data/example.tf');
        $configObject = (new HCLParser($hcl))->parse();

        $expected = unserialize(file_get_contents(__DIR__.'/data/example.tf.expected'));

        $this->assertEquals($expected, $configObject);
    }

    public function testHclCannotEscapeIntoAShellCommand()
    {
        $marker = sys_get_temp_dir().'/php-hcl-parser-command-injection';
        @unlink($marker);

        try {
            (new HCLParser("invalid\nEOF\n; touch ".$marker."\nEOF"))->parse();
            $this->fail('Invalid HCL should fail to parse.');
        } catch (HCLParseException $exception) {
            $this->assertFileDoesNotExist($marker);
        }
    }
}

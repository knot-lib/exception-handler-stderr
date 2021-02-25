<?php
declare(strict_types=1);

namespace KnotLib\ExceptionHandler\Stderr\Test;

use ReflectionException;
use ReflectionClass;
use Exception;

use KnotLib\ExceptionHandler\Stderr\StdErrorExceptionHandler;
use KnotLib\ExceptionHandler\Text\TextDebugtraceRenderer;
use PHPUnit\Framework\TestCase;

final class StdErrorExceptionHandlerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHandleException()
    {
        $renderer = new TextDebugtraceRenderer();
        $handler = new StdErrorExceptionHandler($renderer);

        $fp = fopen('php://memory',"r+");

        $prop = (new ReflectionClass($handler))->getProperty('output');
        $prop->setAccessible(true);
        $prop->setValue($handler, $fp);

        $e = new Exception("test");

        $handler->handleException($e);

        rewind($fp);
        $output = fread($fp,10000);

        $output = explode(PHP_EOL, $output);

        $this->assertEquals('=============================================================', $output[0] ?? null);
        $this->assertEquals('Exception stack trace', $output[1] ?? null);
        $this->assertEquals('=============================================================', $output[2] ?? null);
        $this->assertEquals('', $output[3] ?? null);
        $this->assertEquals('* Exception Stack *', $output[4] ?? null);
        $this->assertEquals('-------------------------------------------------------------', $output[5] ?? null);
        $this->assertEquals('[1]Exception', $output[6] ?? null);
        $this->assertEquals('   test', $output[8] ?? null);
    }
}
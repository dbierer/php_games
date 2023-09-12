<?php
namespace PhpTrainingTest\Maze;

use PhpTraining\Maze\Square;
use PHPUnit\Framework\TestCase;
class SquareTest extends TestCase
{
    public $config = [];
    public $args   = [];
    public function setUp() : void
    {
        $this->config = include __DIR__ . '/../../config/test.config.php';
        $this->args = [
            'type'    => 'TEST',
            'tag'     => 'TEST',
            'css'     => ['test'   => 'TEST'],
            'content' => 'TEST',
            'x'       => 999,
            'y'       => 999
        ];
    }
    public function testInit()
    {
        $square = new Square($this->config, $this->args);
        $expected = 'TEST';
        $actual   = $square->type;
        $this->assertEquals($expected, $actual);
    }
    public function testGetId()
    {
        $square = new Square($this->config, $this->args);
        $expected = '999_999';
        $actual   = $square->getId();
        $this->assertEquals($expected, $actual);
    }
    public function testRenderAsHtml()
    {
        $square = new Square($this->config, $this->args);
        $expected = '<TEST id="999_999" class="col square" >TEST</TEST>';
        $actual   = $square->renderAsHtml();
        $this->assertEquals($expected, $actual);
    }
}

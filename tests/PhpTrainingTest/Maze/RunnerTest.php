<?php
namespace PhpTrainingTest\Maze;

use PhpTraining\Maze\{Runner,Constants};
use PHPUnit\Framework\TestCase;
class RunnerTest extends TestCase
{
    public $config = [];
    public $runner = NULL;
    public function setUp() : void
    {
        $this->config = include __DIR__ . '/../../config/test.config.php';
        $run_args = [
            'avatar' => BASE_DIR . '/public/images/avatar/geek.png',
            'id'     => 101,
            'name'   => 'JR',
            'trail'  => [[0,0],[1,1],[2,2]],
            'content' => Constants::DEF_RUNNER_CONTENT,
            'language' => 'TH',
            'direction' => Constants::NORTH,
            'avatar_class' => 'avatar',
            'x' => 2,
            'y' => 1,
        ];
        $this->runner = new Runner($this->config, $run_args);
    }
    public function testInit()
    {
        $expected = 'JR';
        $actual   = $this->runner->name;
        $this->assertEquals($expected, $actual);
    }
    public function testGetContent()
    {
        $expected = '<img class="avatar" src="/images/avatar/geek.png"/>';
        $actual   = $this->runner->getContent();
        $this->assertEquals($expected, $actual);
    }
}

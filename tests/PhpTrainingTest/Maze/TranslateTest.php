<?php
namespace PhpTrainingTest\Maze;

use PhpTraining\Maze\{Translate,Constants};
use PHPUnit\Framework\TestCase;
class TranslateTest extends TestCase
{
    public $config = [];
    public $trans  = NULL;
    public $lang   = Constants::DEF_LANG;
    public function setUp() : void
    {
        $this->config = include __DIR__ . '/../../config/test.config.php';
        $this->trans  = new Translate($this->config, ['lang' => Constants::DEF_LANG]);
    }
    public function testSayRepeatsTranslation()
    {
        $expected = $this->trans->config[$this->lang][Constants::FORM_LANG];
        $actual   = $this->trans->say(Constants::FORM_LANG);
        $this->assertEquals($expected, $actual);
    }
    public function testSayReturnsKeyStringIfKeyNotFound()
    {
        $expected = 'TEST';
        $actual   = $this->trans->say('TEST');
        $this->assertEquals($expected, $actual);
    }
}

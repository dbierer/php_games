<?php
namespace PhpTrainingTest\Maze;

use FileCMS\Common\Generic\Messages;
use PhpTraining\Maze\{Map,Runner,Plan,Constants,Translate};
use PHPUnit\Framework\TestCase;
class PlanTest extends TestCase
{
    public $config = [];
    public $map    = NULL;
    public $runner = NULL;
    public function setUp() : void
    {
        $this->config = include __DIR__ . '/../../config/test.config.php';
        $map_args = [
            'name' => 'TEST',
            'size' => 99,
            'render' => 'table',
            'doors' => [
                Constants::SOUTH => [[1,0],[2,0],[3,0]],
                Constants::NORTH => [[99,100],[98,100],[97,100]],
            ],
            'inner' => function () {
                $wall = [];
                for ($y = 33; $y < 36; $y++)
                    for ($x = 1; $x < 66; $x++) $wall[] = [$x,$y];
                for ($y = 66; $y < 69; $y++)
                    for ($x = 33; $x < 99; $x++) $wall[] = [$x,$y];
                return $wall;
            },
        ];
        $this->map = new Map($this->config, $map_args);
        $run_args = [
            'avatar' => BASE_DIR . '/public/images/avatar/geek.png',
            'id'     => 101,
            'name'   => 'JR',
            'trail'  => [[2,2],[2,3],[2,4]],
            'content' => Constants::DEF_RUNNER_CONTENT,
            'language' => 'EN',
            'direction' => Constants::NORTH,
            'avatar_class' => 'avatar',
            'start' => [2,0],   // South Door
            'winner' => [98,100],   // North Door
            'x' => 2,
            'y' => 0,
        ];
        $this->runner = new Runner($this->config, $run_args);
        $this->map->buildMap($this->runner);
        $plan_args = [
            'map'  => $this->map,
            'runner' => $this->runner,
        ];
        $this->plan = new Plan($this->config, $plan_args);
    }
    public function testInit()
    {
        $this->assertEquals(Map::class, get_class($this->plan->map));
        $this->assertEquals(Runner::class, get_class($this->plan->runner));
    }
/*
     *  WHILE NOT_SEE_WALL GO
     *  WHILE NOT_SEE_WALL LEFT
     *  WHILE NOT_SEE_WALL RIGHT
     *  WHILE SEE_WALL GO
     *  WHILE SEE_WALL LEFT
     *  WHILE SEE_WALL RIGHT
*/
    public function testPlanWhileNotSeeWallGo()
    {
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[2][6] = $this->map->getWallSquare(2,6);
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_GO];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $expected = [2,5];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
        $this->assertEquals(5, count($steps), 'Number of steps is not correct');
        $this->assertEquals($this->map->grid[$x][$y]->type, Constants::TYPE_TRAIL, 'Should be TRAIL');
        $this->assertEquals($this->map->grid[$x][$y + 5]->type, Constants::TYPE_RUNNER, 'Should be RUNNER');
        $ptn = '!<div id="' . $x . '_' . ($y + 5) . '".*?>(.*?)</div>!';
        end($steps);
        preg_match($ptn, current($steps), $matches);
        $expected = '<img class="avatar" src="/images/avatar/geek.png"/>';
        $actual   = $matches[1];
        $this->assertEquals($expected, $actual, 'HTML render is wrong');
    }
    public function testPlanWhileNotSeeWallLeft()
    {
        $this->runner->x = 3;
        $this->runner->y = 30;
        $this->runner->direction = Constants::NORTH;
        // assign normal squares N, W, S and wall to the E
        $this->map->grid[3][31] = $this->map->getNormalSquare(3, 31);
        $this->map->grid[2][30] = $this->map->getNormalSquare(2, 30);
        $this->map->grid[3][29] = $this->map->getNormalSquare(3, 29);
        $this->map->grid[4][30] = $this->map->getWallSquare(4, 30);
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_LEFT];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $this->assertEquals(Constants::EAST, $this->runner->direction);
        $this->assertEquals(3, count($steps));
    }
    public function testPlanWhileNotSeeWallRight()
    {
        $this->runner->x = 3;
        $this->runner->y = 30;
        $this->runner->direction = Constants::NORTH;
        // assign normal squares N, E, S and wall to the W
        $this->map->grid[3][31] = $this->map->getNormalSquare(3, 31);
        $this->map->grid[2][30] = $this->map->getWallSquare(2, 30);
        $this->map->grid[3][29] = $this->map->getNormalSquare(3, 29);
        $this->map->grid[4][30] = $this->map->getNormalSquare(4, 30);
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_RIGHT];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $this->assertEquals(Constants::WEST, $this->runner->direction);
        $this->assertEquals(3, count($steps));
    }
    public function testPlanWhileSeeWallGo()
    {
        $this->runner->x = 2;
        $this->runner->y = 32;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[2][33] = $this->map->getWallSquare(2,33);
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_GO];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $expected = [2,32];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testPlanWhileSeeWallLeft()
    {
        $this->runner->x = 3;
        $this->runner->y = 30;
        $this->runner->direction = Constants::NORTH;
        // assign wall squares N, W, S and normal to the E
        $this->map->grid[3][31] = $this->map->getWallSquare(3, 31);
        $this->map->grid[2][30] = $this->map->getWallSquare(2, 30);
        $this->map->grid[3][29] = $this->map->getWallSquare(3, 29);
        $this->map->grid[4][30] = $this->map->getNormalSquare(4, 30);
        $args = [Constants::PLAN_SEE_WALL,Constants::PLAN_LEFT];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $this->assertEquals(Constants::EAST, $this->runner->direction);
        $this->assertEquals(3, count($steps));
    }
    public function testPlanWhileSeeWallRight()
    {
        $this->runner->x = 3;
        $this->runner->y = 30;
        $this->runner->direction = Constants::NORTH;
        // assign wall squares N, E, S and normal to the W
        $this->map->grid[3][31] = $this->map->getWallSquare(3, 31);
        $this->map->grid[4][30] = $this->map->getWallSquare(4, 30);
        $this->map->grid[3][29] = $this->map->getWallSquare(3, 29);
        $this->map->grid[2][30] = $this->map->getNormalSquare(2, 30);
        $args = [Constants::PLAN_SEE_WALL,Constants::PLAN_RIGHT];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $this->assertEquals(Constants::WEST, $this->runner->direction);
        $this->assertEquals(3, count($steps));
    }
    public function testPlanWhileBadCmdReturnsMessage()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $saved = clone $this->runner;
        $args = ['BAD',Constants::PLAN_GO];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $this->assertEquals($saved, $this->runner);
        $expected = 'WHILE BAD ' . Constants::PLAN_GO . "<br />\nBad command";
        $actual   = $message->getMessages();
        $this->assertEquals($expected, $actual);
    }
    public function testPlanWhileBadCmdOptsReturnsMessage()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $saved = clone $this->runner;
        $args = [Constants::PLAN_NOT_SEE_WALL,'BAD'];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $this->assertEquals($saved, $this->runner);
        $expected = "WHILE NOT_SEE_WALL BAD<br />\nBad command";
        $actual   = $message->getMessages();
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfSeeWallGo()
    {
        $x = 2;
        $y = 32;
        $this->runner->x = $x;
        $this->runner->y = $y;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[$x][$y+1] = $this->map->getWallSquare($x,$y+1);
        $args = [Constants::PLAN_SEE_WALL,Constants::PLAN_GO];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = [$x,$y];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
        $this->assertEquals(TRUE, $this->map->hitWall);
    }
    public function testPlanIfSeeWallLeft()
    {
        $this->runner->x = 2;
        $this->runner->y = 32;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[2][33] = $this->map->getWallSquare(2,33);
        $args = [Constants::PLAN_SEE_WALL,Constants::PLAN_LEFT];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = Constants::WEST;
        $actual   = $this->runner->direction;
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfSeeWallRight()
    {
        $this->runner->x = 2;
        $this->runner->y = 32;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[2][33] = $this->map->getWallSquare(2,33);
        $args = [Constants::PLAN_SEE_WALL,Constants::PLAN_RIGHT];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = Constants::EAST;
        $actual   = $this->runner->direction;
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfNotSeeWallGo()
    {
        $x = 2;
        $y = 32;
        $this->runner->x = $x;
        $this->runner->y = $y;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[$x][$y+1] = $this->map->getNormalSquare($x,$y+1);
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_GO];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = [$x,$y+1];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfNotSeeWallLeft()
    {
        $x = 2;
        $y = 32;
        $this->runner->x = $x;
        $this->runner->y = $y;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[$x][$y+1] = $this->map->getNormalSquare($x,$y+1);
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_LEFT];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = Constants::WEST;
        $actual   = $this->runner->direction;
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfNotSeeWallRight()
    {
        $x = 2;
        $y = 32;
        $this->runner->x = $x;
        $this->runner->y = $y;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[$x][$y+1] = $this->map->getNormalSquare($x,$y+1);
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_RIGHT];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = Constants::EAST;
        $actual   = $this->runner->direction;
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfReturnsErrMessageIfCondIsBad()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $args = ['BAD',Constants::PLAN_RIGHT];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = 'IF BAD RIGHT<br />' . PHP_EOL . 'Bad command';
        $actual   = $message->getMessages();
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfReturnsErrMessageIfOptIsBad()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $x = 2;
        $y = 32;
        $this->runner->x = $x;
        $this->runner->y = $y;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[$x][$y+1] = $this->map->getWallSquare($x,$y+1);
        $args = [Constants::PLAN_SEE_WALL,'BAD'];
        $steps = [];
        $result = $this->plan->planIf($args, $steps);
        $expected = 'IF SEE_WALL BAD<br />' . "\n" . 'Bad command';
        $actual   = $message->getMessages();
        $this->assertEquals($expected, $actual);
    }
    public function testPlanForGo()
    {
        $num = 7;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $args = [$num, Constants::PLAN_GO];
        $steps = [];
        $this->plan->planFor($args, $steps);
        $expected = [$x,$y + $num];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
        $this->assertEquals($num, count($steps));
    }
    public function testPlanForGoMakesCurrentPosRunnerSquare()
    {
        $num = 3;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $args = [$num, Constants::PLAN_GO];
        $steps = [];
        $this->plan->planFor($args, $steps);
        $expected = Constants::TYPE_RUNNER;
        $actual   = $this->map->grid[$x][$y + $num]->type;
        $this->assertEquals($expected, $actual);
    }
    public function testPlanForLeft()
    {
        $num = 3;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $args = [$num, Constants::PLAN_LEFT];
        $steps = [];
        $this->plan->planFor($args, $steps);
        $expected = Constants::EAST;
        $actual   = $this->runner->direction;
        $this->assertEquals($expected, $actual);
    }
    public function testPlanForRight()
    {
        $num = 3;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $args = [$num, Constants::PLAN_RIGHT];
        $steps = [];
        $this->plan->planFor($args, $steps);
        $expected = Constants::WEST;
        $actual   = $this->runner->direction;
        $this->assertEquals($expected, $actual);
    }
    public function testPlanForReturnsErrMessageIfActionIsBad()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $args = [3, 'BAD'];
        $steps = [];
        $this->plan->planFor($args, $steps);
        $expected = 'FOR 3 BAD<br />' . "\n" . 'Bad command';
        $actual   = $message->getMessages();
        $this->assertEquals($expected, $actual);
    }
    public function testPlanForReturnsErrMessageIfNumIsBad()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $args = [0, Constants::PLAN_GO];
        $steps = [];
        $this->plan->planFor($args, $steps);
        $expected = 'FOR 0 GO<br />' . "\n" . 'Bad command';
        $actual   = $message->getMessages();
        $this->assertEquals($expected, $actual);
    }
    public function testPlanIfNotSeeWallGoDoesNotGoOffGrid()
    {
        $this->runner->x = 2;
        $this->runner->y = 0;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $args = [Constants::PLAN_NOT_SEE_WALL,Constants::PLAN_GO];
        $steps = [];
        $this->plan->planWhile($args, $steps);
        $expected = [2,$this->map->size - 1];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testRunGo()
    {
        $plan = 'GO';
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $steps = $this->plan->run($plan);
        $expected = [$x, $y + 1];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
        $this->assertEquals(2, count($steps), 'Number of steps is not correct');
        $this->assertEquals($this->map->grid[$x][$y]->type, Constants::TYPE_TRAIL);
        $this->assertEquals($this->map->grid[$x][$y + 1]->type, Constants::TYPE_RUNNER);
    }
    public function testRunRight()
    {
        $plan = 'RIGHT';
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $this->plan->run($plan);
        $this->assertEquals(Constants::EAST, $this->runner->direction);
    }
    public function testRunWhile()
    {
        $plan = 'WHILE NOT_SEE_WALL GO';
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->plan->run($plan);
        $expected = [2, $this->map->size - 1];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testRunFor()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $offset = 7;
        $plan = <<<EOT
FOR $offset GO
EOT;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $steps = $this->plan->run($plan);
        $expected = [$x, $y + $offset];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
        $this->assertEquals($offset + 1, count($steps), 'Number of steps is not correct');
        $this->assertEquals($this->map->grid[$x][$y]->type, Constants::TYPE_TRAIL, 'Should be TRAIL');
        $this->assertEquals($this->map->grid[$x][$y + $offset]->type, Constants::TYPE_RUNNER, 'Should be RUNNER');
        $ptn = '!<div id="' . ($x) . '_' . ($y + $offset) . '".*?>(.*?)</div>!';
        end($steps);
        preg_match($ptn, current($steps), $matches);
        $expected = '<img class="avatar" src="/images/avatar/geek.png"/>';
        $actual   = $matches[1];
        $this->assertEquals($expected, $actual, 'HTML render is wrong');
        $this->assertEquals('', $message->getMessages());
    }
    public function testRunIfSeeWallRightGo()
    {
        $plan = <<<EOT
IF SEE_WALL RIGHT
GO
EOT;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = $this->map->size - 1;
        $this->runner->direction = Constants::NORTH;
        $steps = $this->plan->run($plan);
        $expected = [$x + 1, $y];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testRunWhileNotSeeWallGoDirectionEast()
    {
        $plan = <<<EOT
WHILE NOT_SEE_WALL GO
EOT;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = $this->map->size - 1;
        $this->runner->direction = Constants::EAST;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $sq = $this->map->grid[$x + 1][$y];
        $this->assertEquals(Constants::TYPE_NORMAL, $sq->type);
        $steps = $this->plan->run($plan);
        $expected = [$this->map->size - 1, $y];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testRunMultipleFor()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $offset = 7;
        $plan = <<<EOT
FOR $offset GO
RIGHT
FOR $offset GO
EOT;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $steps = $this->plan->run($plan);
        $this->assertEquals('', $message->getMessages());
        $this->assertEquals(Constants::EAST, $this->runner->direction, 'Wrong direction');
        $this->assertEquals(($offset + 1) * 2, count($steps), 'Number of steps is not correct');
        $expected = [$x + $offset, $y + $offset];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testRunMultipleWhile()
    {
        $message = Messages::getInstance();
        $message->messages = [];   // clears messages
        $plan = <<<EOT
WHILE NOT_SEE_WALL GO
IF SEE_WALL RIGHT
WHILE NOT_SEE_WALL GO
EOT;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $steps = $this->plan->run($plan);
        $this->assertEquals('', $message->getMessages());
        $this->assertEquals(Constants::EAST, $this->runner->direction, 'Wrong direction');
        $this->assertEquals((($this->map->size - 1) * 2) - $x + 2, count($steps), 'Number of steps is not correct');
        $expected = [$this->map->size - 1, $this->map->size - 1];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
    }
    public function testRunBigPlan()
    {
        $offset = 7;
        $plan = <<<EOT
FOR $offset GO
RIGHT
WHILE NOT_SEE_WALL GO
LEFT
WHILE NOT_SEE_WALL GO
EOT;
        $this->runner->x = $x = 2;
        $this->runner->y = $y = 0;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $steps = $this->plan->run($plan);
        $expected = [$this->map->size - $x + 1, $y + $offset];
        $actual   = [$this->runner->x, $this->runner->y];
        $this->assertEquals($expected, $actual);
        $this->assertEquals($offset + $this->map->size - 1, count($steps), 'Number of steps is not correct');
        $this->assertEquals($this->map->grid[$x][$y]->type, Constants::TYPE_TRAIL, 'Should be TRAIL');
        $this->assertEquals($this->map->grid[$this->runner->x][$this->runner->y]->type, Constants::TYPE_RUNNER, 'Should be RUNNER');
    }
}

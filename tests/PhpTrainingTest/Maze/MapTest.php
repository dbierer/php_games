<?php
namespace PhpTrainingTest\Maze;

use PhpTraining\Maze\{Map,Square,Constants,Runner};
use PHPUnit\Framework\TestCase;
class MapTest extends TestCase
{
    public $config = [];
    public $map    = NULL;
    public $runner = NULL;
    public function setUp() : void
    {
        $this->config = include __DIR__ . '/../../config/test.config.php';
        $map_args = ['name' => 'TEST'];
        $this->map = new Map($this->config, $map_args);
        $run_args = [
            'avatar' => BASE_DIR . '/public/images/avatar/geek.png',
            'id'     => 101,
            'name'   => 'JR',
            'trail'  => [[2,2],[2,3],[2,4]],
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
        $expected = 'TEST';
        $actual   = $this->map->name;
        $this->assertEquals($expected, $actual);
    }
    public function testInitCreatesInnerArrayIfInnerIsCallable()
    {
        $expected = TRUE;
        $actual   = is_array($this->map->inner);
        $this->assertEquals($expected, $actual);
    }
    public function testGetNormalSquare()
    {
        $square = $this->map->getNormalSquare(999,999);
        $class  = Square::class;
        $this->assertEquals(TRUE, ($square instanceof $class));
        $this->assertEquals('999_999', $square->getId());
        $this->assertEquals(Constants::TYPE_NORMAL, $square->type);
    }
    public function testGetWallSquare()
    {
        $square = $this->map->getWallSquare(999,999);
        $class  = Square::class;
        $this->assertEquals(TRUE, ($square instanceof $class));
        $this->assertEquals('999_999', $square->getId());
        $this->assertEquals(Constants::TYPE_WALL, $square->type);
    }
    public function testGetTrailSquare()
    {
        $square = $this->map->getTrailSquare(999,999);
        $class  = Square::class;
        $this->assertEquals(TRUE, ($square instanceof $class));
        $this->assertEquals('999_999', $square->getId());
        $this->assertEquals(Constants::TYPE_TRAIL, $square->type);
    }
    public function testGetRunnerSquare()
    {
        $square = $this->map->getRunnerSquare($this->runner);
        $class  = Square::class;
        $this->assertEquals(TRUE, ($square instanceof $class));
        $this->assertEquals('2_1', $square->getId());
        $this->assertEquals(Constants::TYPE_RUNNER, $square->type);
    }
    public function testBuildBlankGridWalls()
    {
        $grid = $this->map->buildBlankGrid();
        $middle = (int) ($this->map->size / 2);
        $this->assertEquals(Constants::TYPE_WALL, $grid[0][$middle]->type, 'Western Wall Not OK');
        $this->assertEquals(Constants::TYPE_WALL, $grid[$this->map->size][$middle]->type, 'Eastern Wall Not OK');
        $this->assertEquals(Constants::TYPE_WALL, $grid[$middle][0]->type, 'Southern Wall Not OK');
        $this->assertEquals(Constants::TYPE_WALL, $grid[$middle][$this->map->size]->type, 'Northern Wall Not OK');
    }
    public function testBuildBlankGridMiddle()
    {
        $grid = $this->map->buildBlankGrid();
        $middle = (int) ($this->map->size / 2);
        $expected = '<div id="'
                  . $middle . '_' . $middle
                  . '" class="col square" ><img src="/images/normal.png" class="std-img"/></div>';
        $actual   = $grid[$middle][$middle]->renderAsHtml();
        $this->assertEquals($expected, $actual, 'Middle Not OK');
    }
    public function testDrawDoors()
    {
        $this->map->buildBlankGrid();
        // 1,0 should be a wall
        $this->assertEquals(Constants::TYPE_WALL, $this->map->grid[1][0]->type);
        $this->map->drawDoors();
        // 1,0 should now be normal (e.g. a door)
        $this->assertEquals(Constants::TYPE_WALL, $this->map->grid[0][0]->type);
        $this->assertEquals(Constants::TYPE_NORMAL, $this->map->grid[1][0]->type);
    }
    public function testDrawInnerWall()
    {
        $this->map->inner = [[1,33],[2,33],[3,33]];
        $this->map->buildBlankGrid();
        // 1,33 should be a normal
        $this->assertEquals(Constants::TYPE_NORMAL, $this->map->grid[1][33]->type);
        $this->map->drawInnerWalls();
        // 1,33 should now be a wall
        $this->assertEquals(Constants::TYPE_WALL, $this->map->grid[1][33]->type);
    }
    public function testPlaceRunner()
    {
        $x = $this->runner->x;
        $y = $this->runner->y;
        $this->map->buildBlankGrid();
        // should be a normal
        $this->assertEquals(Constants::TYPE_NORMAL, $this->map->grid[$x][$y]->type);
        $this->map->placeRunner($this->runner);
        // should now be a wall
        $this->assertEquals(Constants::TYPE_RUNNER, $this->map->grid[$x][$y]->type);
    }
    public function testMoveRunnerIncrementsXWhenMovingEast()
    {
        $x = $this->runner->x;
        $y = $this->runner->y;
        $this->runner->direction = Constants::EAST;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $this->map->moveRunner($this->runner);
        $this->assertEquals($x + 1, $this->runner->x);
    }
    public function testMoveRunnerIncrementsYWhenMovingNorth()
    {
        $x = $this->runner->x = 1;
        $y = $this->runner->y = 1;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $this->map->moveRunner($this->runner);
        $this->assertEquals($y + 1, $this->runner->y);
    }
    public function testMoveRunnerDecrementsXWhenMovingWest()
    {
        $x = $this->runner->x = 2;
        $y = $this->runner->y = 1;
        $this->runner->direction = Constants::WEST;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $this->map->moveRunner($this->runner);
        $this->assertEquals($x - 1, $this->runner->x);
    }
    public function testMoveRunnerDecrementsYWhenMovingSouth()
    {
        $x = $this->runner->x = 1;
        $y = $this->runner->y = 2;
        $this->runner->direction = Constants::SOUTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $this->map->moveRunner($this->runner);
        $this->assertEquals($y - 1, $this->runner->y);
    }
    public function testMoveRunnerMarksTrail()
    {
        $x = $this->runner->x = 1;
        $y = $this->runner->y = 1;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->assertEquals(Constants::TYPE_NORMAL, $this->map->grid[$x][$y]->type);
        $this->map->placeRunner($this->runner);
        $this->map->moveRunner($this->runner);
        $this->assertEquals(Constants::TYPE_TRAIL, $this->map->grid[$x][$y]->type);
    }
    public function testMoveRunnerReturnsTrueWhenClear()
    {
        $x = $this->runner->x = 1;
        $y = $this->runner->y = 1;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $this->assertEquals(Constants::TYPE_RUNNER, $this->map->grid[$this->runner->x][$this->runner->y]->type);
        $this->map->moveRunner($this->runner);
        $this->map->placeRunner($this->runner);
        $this->assertEquals(Constants::TYPE_TRAIL, $this->map->grid[$x][$y]->type);
        $this->assertEquals(Constants::TYPE_RUNNER, $this->map->grid[$this->runner->x][$this->runner->y]->type);
    }
    public function testMoveRunnerReturnsFalseWhenFacingWall()
    {
        $x = $this->runner->x = 1;
        $y = $this->runner->y = 32;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $this->map->grid[$x][33] = $this->map->getWallSquare(1,33);
        $expected = FALSE;
        $actual   = $this->map->moveRunner($this->runner);
        $this->assertEquals($expected, $actual);
    }
    public function testMoveRunnerHitWallReturnsTrueWhenFacingWall()
    {
        $x = $this->runner->x = 1;
        $y = $this->runner->y = 32;
        $this->runner->direction = Constants::NORTH;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $this->map->grid[$x][$y + 1] = $this->map->getWallSquare($x, $y + 1);
        $this->map->moveRunner($this->runner);
        $expected = TRUE;
        $actual   = $this->runner->hitWall;
        $this->assertEquals($expected, $actual);
    }
    public function testMoveRunnerReturnsFalseWhenFacingEdgeWall()
    {
        $x = $this->runner->x = 1;
        $y = $this->runner->y = 1;
        $this->runner->direction = Constants::WEST;
        $this->map->buildBlankGrid();
        $this->map->placeRunner($this->runner);
        $expected = FALSE;
        $actual   = $this->map->moveRunner($this->runner);
        $this->assertEquals($expected, $actual);
    }
    public function testBuildMapReturnsSelf()
    {
        $map = $this->map->buildMap($this->runner);
        $expected = Map::class;
        $actual   = get_class($map);
        $this->assertEquals($expected, $actual);
    }
    public function testBuildMapPlacesExpectedArtifacts()
    {
        $map = $this->map->buildMap($this->runner);
        [$x,$y] = $this->runner->trail[0];
        $this->assertEquals($map->grid[0][0]->type, Constants::TYPE_WALL, 'Not a wall');
        $this->assertEquals($map->grid[1][1]->type, Constants::TYPE_NORMAL, 'Not normal');
        $this->assertEquals($map->grid[1][33]->type, Constants::TYPE_WALL, 'Missing inner wall');
        $this->assertEquals($map->grid[$x][$y]->type, Constants::TYPE_TRAIL, 'Trail not correct');
        $this->assertEquals($map->grid[$this->runner->x][$this->runner->y]->type, Constants::TYPE_RUNNER, 'Missing runner');
    }
    public function testBuildMapPlacesRunnerAtStart()
    {
        $map = $this->map->buildMap($this->runner);
        [$x, $y] = Constants::DEF_MAP_START;
        $expected = Constants::TYPE_RUNNER;
        $actual   = $this->map->grid[$x][$y]->type;
        $this->assertEquals($expected, $actual);
    }
    public function testSeeWhat()
    {
        $this->runner->x = 2;
        $this->runner->y = 32;
        $this->map->grid[2][33] = $this->map->getWallSquare(2,33);
        $expected = Constants::SEE_WALL;
        $actual   = $this->map->seeWhat($this->runner);
        $this->assertEquals($expected, $actual);
    }
    public function testSeeWall()
    {
        $this->runner->x = 2;
        $this->runner->y = 32;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[2][33] = $this->map->getWallSquare(2,33);
        $expected = TRUE;
        $actual   = $this->map->seeWall($this->runner);
        $this->assertEquals($expected, $actual);
    }
    public function testNotSeeWall()
    {
        $this->runner->x = 2;
        $this->runner->y = 32;
        $this->runner->direction = Constants::NORTH;
        $this->map->grid[2][33] = $this->map->getNormalSquare(2,33);
        $expected = FALSE;
        $actual   = $this->map->seeWall($this->runner);
        $this->assertEquals($expected, $actual);
    }
    public function testNotSeeWallFacingEastAtTop()
    {
        $this->runner->x = 2;
        $this->runner->y = $this->map->size - 1;
        $this->runner->direction = Constants::EAST;
        $this->map->buildBlankGrid();
        $expected = FALSE;
        $actual   = $this->map->seeWall($this->runner);
        $this->assertEquals($expected, $actual);
    }
    public function testPlotGoCalculatesNorthCorrectly()
    {
        $this->runner->x = 2;
        $this->runner->y = 2;
        $this->runner->direction = Constants::NORTH;
        $expected = [2,3];
        $actual   = $this->map->plotGo($this->runner);
        $this->assertEquals($expected, $actual);
    }
}

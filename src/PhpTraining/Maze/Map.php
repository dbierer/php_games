<?php
namespace PhpTraining\Maze;
/*
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 * * Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above
 *   copyright notice, this list of conditions and the following disclaimer
 *   in the documentation and/or other materials provided with the
 *   distribution.
 * * Neither the name of the  nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

use ArrayIterator;
use InfiniteIterator;
use FileCMS\Common\Generic\Messages;
class Map extends Base
{
    public $name    = Constants::DEF_MAP_NAME;
    public $size    = Constants::DEF_GRID_SIZE;
    public $square  = [];
    public $grid    = [];
    public $doors   = [];
    public $inner   = [];
    public $start   = [];
    public $winner  = [];
    public $config  = [];
    public $render  = Constants::DEF_MAP_RENDER;
    public $sound_step = '';
    public $sound_wall = '';
    public $sound_done = '';
    public function init(array $args)
    {
        $this->name   = $args['name'] ?? Constants::DEF_MAP_NAME;
        $this->size   = $args['size'] ?? Constants::DEF_GRID_SIZE;
        $this->doors  = $args['doors'] ?? Constants::DEF_DOORS;
        $this->start  = $args['start'] ?? Constants::DEF_MAP_START;
        $this->winner = $args['winner'] ?? Constants::DEF_MAP_WINNER;
        if (empty($args['inner'])) {
            $this->inner = Constants::DEF_INNER;
        } elseif (is_callable($args['inner'])) {
            $this->inner = $args['inner']();
        } else {
            $this->inner = $args['inner'];
        }
        // add sound source
        $this->sound_step = $args['sound_step'] ?? Constants::DEF_SND_STEP;
        $this->sound_done = $args['sound_done'] ?? Constants::DEF_SND_DONE;
        $iter = new ArrayIterator($args['sound_wall'] ?? Constants::DEF_SND_WALL);
        $this->sound_wall = new InfiniteIterator($iter);
        $this->square = $this->config['PHP_TRAINING']['maze']['square'] ?? [];
    }
    public function buildMap(RunnerInterface $runner) : static
    {
        // build maze
        $this->buildBlankGrid();
        $this->drawDoors();
        $this->drawInnerWalls();
        // place runner in starting position
        [$runner->x, $runner->y] = $this->start;
        $this->placeRunner($runner);
        return $this;
    }
    public function buildBlankGrid() : array
    {
        // build Southern Edge
        $y = 0;
        for ($x = 0; $x <= $this->size; $x++)
            $this->grid[$x][$y] = $this->getWallSquare($x, $y);
        // build Northern Edge
        $y = $this->size;
        for ($x = 0; $x <= $this->size; $x++)
            $this->grid[$x][$y] = $this->getWallSquare($x, $y);
        // build Western Edge
        $x = 0;
        for ($y = 0; $y <= $this->size; $y++)
            $this->grid[$x][$y] = $this->getWallSquare($x, $y);
        // build Eastern Edge
        $x = $this->size;
        for ($y = 0; $y <= $this->size; $y++)
            $this->grid[$x][$y] = $this->getWallSquare($x, $y);
        // build middle
        for ($x = 1; $x < $this->size; $x++) {
            for ($y = 1; $y < $this->size; $y++) {
                $this->grid[$x][$y] = $this->getNormalSquare($x, $y);
            }
        }
        return $this->grid;
    }
    public function moveRunner(RunnerInterface $runner) : bool
    {
        $x  = $runner->x;
        $y  = $runner->y;
        // check to see if new square is normal
        if ($this->seeWall($runner)) {
            $msg = $runner->translate->say(Constants::ERR_HIT_WALL);
            (Messages::getInstance())->addMessage($msg);
            $runner->hitWall = TRUE;
            $ok = FALSE;
        } else {
            // set current pos to trail square
            $this->grid[$runner->x][$runner->y] = $this->getTrailSquare($runner->x, $runner->y);
            // update runner trail
            $runner->trail[] = [$runner->x,$runner->y];
            // update runner with new coords
            [$x, $y] = $this->plotGo($runner);
            $runner->x = $x;
            $runner->y = $y;
            // place runner
            $runner->hitWall = FALSE;
            $runner->winner  = $this->isWinner($runner);
            $this->placeRunner($runner);
            $ok = TRUE;
        }
        return $ok;
    }
    /**
     * Tells you what runner sees in next square in current direction
     *
     * @param RunnerInterface
     * @return string : Constants::SEE_WALL|CLEAR|WINNER
     */
    public function seeWhat(RunnerInterface $runner) : string
    {
        // calculate new x,y coordinates based upon direction
        [$x, $y] = $this->plotGo($runner);
        // check to see if new square is off-grid or wall
        if (empty($this->grid[$x][$y])) {
            $start_y = $this->start[1] ?? 0;
            if ($y !== $start_y) {
                $see = Constants::WINNER;
            } else {
                $see = Constants::SEE_WALL;
            }
        } elseif ($this->grid[$x][$y]->type === Constants::TYPE_WALL) {
            $see = Constants::SEE_WALL;
        } else {
            $see = Constants::CLEAR;
        }
        return $see;
    }
    /**
     * Checks to see if runner sees a wall
     *
     * @param RunnerInterface
     * @return bool
     */
    public function seeWall(RunnerInterface $runner)
    {
        return ($this->seeWhat($runner) === Constants::SEE_WALL);
    }
    /**
     * Checks to see if runner is a winner
     *
     * @param RunnerInterface
     * @return bool
     */
    public function isWinner(RunnerInterface $runner)
    {
        $winner = 0;
        foreach ($this->winner as [$x,$y]) {
            $winner += (int) ($x === $runner->x && $y === $runner->y);
        }
        return (bool) $winner;
    }
    /**
     * Plots next X,Y coordinate
     *
     * @param RunnerInterface $runner
     * @return array : [x,y]
     */
    public function plotGo(RunnerInterface $runner) : array
    {
        // calculate new x,y coordinates based upon direction
        switch ($runner->direction) {
            case Constants::SOUTH :
                $x = $runner->x;
                $y = $runner->y - 1;
                break;
            case Constants::EAST :
                $x = $runner->x + 1;
                $y = $runner->y;
                break;
            case Constants::WEST :
                $x = $runner->x - 1;
                $y = $runner->y;
                break;
            case Constants::NORTH :
            default :
                $x = $runner->x;
                $y = $runner->y + 1;
                break;
        }
        return [$x, $y];
    }
    public function placeRunner(RunnerInterface $runner) : void
    {
        // place trail
        if (!empty($runner->trail)) {
            foreach ($runner->trail as [$x, $y])
                $this->grid[$x][$y] = $this->getTrailSquare($x, $y);
        }
        // place runner
        $this->grid[$runner->x][$runner->y] = $this->getRunnerSquare($runner);
    }
    public function drawDoors() : void
    {
        foreach ($this->doors as $key => $coords) {
            foreach ($coords as [$x,$y]) {
                $this->grid[$x][$y] = $this->getNormalSquare($x, $y);
            }
        }
    }
    // inner walls can be expressed as either a callback, or a string of raw coordinates
    public function drawInnerWalls() : void
    {
        foreach ($this->inner as $key => [$x,$y]) {
            $this->grid[$x][$y] = $this->getWallSquare($x, $y);
        }
    }
    // TODO
    public function createThumbNail()
    {
        // creates PNG of what map looks like
    }
    public function getDefaultSquareArgs(string $type, string $content) : array
    {
        return [
            'type'    => $type,
            'tag'     => Constants::DEF_SQ_TAG,
            'content' => $content,
            'css_class' => Constants::DEF_SQ_CLASS,
        ];
    }
    public function getNormalSquare(int $x, int $y) : Square
    {
        $args = $this->square[Constants::TYPE_NORMAL]
              ?? $this->getDefaultSquareArgs(Constants::TYPE_NORMAL, Constants::DEF_NML_CONTENT);
        $args['x'] = $x;
        $args['y'] = $y;
        return new Square($this->config, $args);
    }
    public function getWallSquare(int $x, int $y) : Square
    {
        $args = $this->square[Constants::TYPE_WALL]
              ?? $this->getDefaultSquareArgs(Constants::TYPE_WALL, Constants::DEF_WALL_CONTENT);
        $args['x'] = $x;
        $args['y'] = $y;
        return new Square($this->config, $args);
    }
    public function getTrailSquare(int $x, int $y) : Square
    {
        $args = $this->square[Constants::TYPE_TRAIL]
              ?? $this->getDefaultSquareArgs(Constants::TYPE_TRAIL, Constants::DEF_TRAIL_CONTENT);
        $args['x'] = $x;
        $args['y'] = $y;
        return new Square($this->config, $args);
    }
    public function getRunnerSquare(RunnerInterface $runner) : Square
    {
        $args = [
            'type'    => Constants::TYPE_RUNNER,
            'tag'     => Constants::DEF_SQ_TAG,
            'content' => $runner->getContent(),
            'css_class' => Constants::DEF_SQ_CLASS . ' ' . $runner->direction,
            'x'       => $runner->x,
            'y'       => $runner->y
        ];
        return new Square($this->config, $args);
    }
    /**
     * Adds HTML5 sound element
     *
     * @param RunnerInterface $runner
     * @return string $html
     */
    public function addSound(RunnerInterface $runner) : string
    {
        if ($runner->hitWall === TRUE) {
            $src = $this->sound_wall->current();
            $this->sound_wall->next();
        } elseif ($runner->winner === TRUE) {
            $src = $this->sound_done;
        } else {
            $src = $this->sound_step;
        }
        return '<audio autoplay>'
              . '<source src="' . $src . '" type="audio/mpeg">'
              . 'Your browser does not support the audio element.'
              . '</audio>';
    }
    /**
     * Returns HTML table from $this->grid
     *
     * @param RunnerInterface $runner
     * @param int $width : width as a percentage
     * @return string $html
     */
    public function renderAsTable(RunnerInterface $runner, int $width = 100) : string
    {
        $this->placeRunner($runner);
        $html = $this->addSound($runner);
        $html .= '<table width="' . $width . '%">' . PHP_EOL;
        for ($y = $this->size; $y >= 0; $y--) {
            $html .= '<tr>';
            for ($x = 0; $x <= $this->size; $x++) {
                $html .= $this->grid[$x][$y]->renderAsHtml();
            }
            $html .= '</tr>' . PHP_EOL;
        }
        $html .= '</table>' . PHP_EOL;
        return $html;
    }
    /**
     * Returns HTML table from $this->grid using <div> "row" and "col" classes
     *
     * @param RunnerInterface $runner
     * @return string $html
     */
    public function renderAsDiv(RunnerInterface $runner) : string
    {
        $this->placeRunner($runner);
        $html = '';
        $html .= $this->addSound($runner);
        for ($y = $this->size; $y >= 0; $y--) {
            $html .= '<div class="row">';
            for ($x = 0; $x <= $this->size; $x++) {
                $square = $this->grid[$x][$y] ?? NULL;
                if ($square instanceof Square)
                    $html .= $square->renderAsHtml();
            }
            $html .= '</div>' . PHP_EOL;
        }
        return $html;
    }
}

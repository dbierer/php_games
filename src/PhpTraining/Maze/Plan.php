<?php
namespace PhpTraining\Maze;
/*
 * Interprets a "Plan"
 *
 * @todo: generate PHP code from the plan
 *
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

use Exception;
use PhpTraining\Maze\Translate;
use FileCMS\Common\Generic\Messages;
class Plan extends Base
{
    public $runner = NULL;
    public $map    = NULL;
    public $plan   = [];
    public $config = [];
    public $trans  = NULL;
    public function init(array $args)
    {
        $this->runner = $args['runner'] ?? NULL;
        $this->map    = $args['map']    ?? NULL;
        if (empty($this->runner) || empty($this->map)) {
            throw new Exception($this->trans->say(Constants::ERR_PLAN));
        }
        $this->trans  = $this->runner->translate;
    }
    public function __destruct()
    {
        $_SESSION[__CLASS__] = $this;
    }
    /**
     * Interprets and runs plan
     *
     * @mixed $plan
     * @return array $steps
     */
    public function run($plan = '')
    {
        if (!empty($plan)) {
            if (is_string($plan)) {
                $this->plan = explode(PHP_EOL, $plan);
            } elseif (is_array($plan)) {
                $this->plan = $plan;
            } else {
                $this->plan = [];
            }
        }
        $steps = [];
        $this->map->placeRunner($this->runner);
        $steps[] = $this->map->renderAsDiv($this->runner);
        foreach ($this->plan as $instruction) {
            if (empty($instruction)) continue;
            $args = explode(' ', strtoupper($instruction));
            if (empty($args[0])) continue;
            $token = strtoupper(trim(array_shift($args)));
            if (empty($token)) continue;
            switch ($token) {
                case Constants::PLAN_WHILE :
                    $this->planWhile($args, $steps);
                    break;
                case Constants::PLAN_FOR :
                    $this->planFor($args, $steps);
                    break;
                case Constants::PLAN_IF :
                    $this->planIf($args, $steps);
                    break;
                case Constants::PLAN_LEFT :
                    $this->runner->direction = Constants::PLAN_TURN_LEFT[$this->runner->direction];
                    $steps[] = $this->map->renderAsDiv($this->runner);
                    break;
                case Constants::PLAN_RIGHT :
                    $this->runner->direction = Constants::PLAN_TURN_RIGHT[$this->runner->direction];
                    $steps[] = $this->map->renderAsDiv($this->runner);
                    break;
                case Constants::PLAN_GO :
                    $this->map->moveRunner($this->runner);
                    $steps[] = $this->map->renderAsDiv($this->runner);
                    break;
                default :
                    (Messages::getInstance())->addMessage($this->trans->say(Constants::ERR_CMD));
                    (Messages::getInstance())->addMessage($instruction);
            }
        }
        return $steps;
    }
    /**
     * Checks when to stop
     *
     * @param RunnerInterface $runner
     * @return bool : TRUE = OK; FALSE = not OK
     */
    public function checkStop(RunnerInterface $runner)
    {
        $stop = 0;
        $stop += ($runner->x < 0) ? 1 : 0;
        $stop += ($runner->y < 0) ? 1 : 0;
        $stop += ($runner->x > $this->map->size) ? 1 : 0;
        $stop += ($runner->y > $this->map->size) ? 1 : 0;
        return ($stop === 0);
    }
    /**
     * Returns $args value trimmed or empty string
     *
     * @param array $args
     * @param int   $idx
     * @return string $value
     */
    public function getArg(array $args, int $idx)
    {
        $value = $args[$idx] ?? '';
        return trim($value);
    }
    /**
     * Exercises these possible instructions:
     *  WHILE NOT_SEE_WALL GO
     *  WHILE NOT_SEE_WALL LEFT
     *  WHILE NOT_SEE_WALL RIGHT
     *  WHILE SEE_WALL GO
     *  WHILE SEE_WALL LEFT
     *  WHILE SEE_WALL RIGHT
     *
     * Note that the "WHILE SEE_WALL GO" instruction results in an error!
     * But we allow this as part of the learning process
     *
     * @param array $args
     * @param array $steps
     * @return array $steps
     */
    public function planWhile(array $args, array &$steps)
    {
        $good_cmd  = TRUE;
        $condition = $this->getArg($args, 0);
        $action    = $this->getArg($args, 1);
        $count     = 0;
        $stop      = FALSE;
        error_log(__METHOD__  . ':' . $condition . ':' . $action);
        if ($condition === Constants::PLAN_SEE_WALL) {
            switch ($action) {
                case Constants::PLAN_LEFT :
                    while ($this->map->seeWall($this->runner) && !$stop) {
                        $this->runner->direction = Constants::PLAN_TURN_LEFT[$this->runner->direction];
                        $steps[] = $this->map->renderAsDiv($this->runner);
                        $stop = ($count++ > $this->map->size);
                    }
                    break;
                case Constants::PLAN_RIGHT :
                    while ($this->map->seeWall($this->runner) && !$stop) {
                        $this->runner->direction = Constants::PLAN_TURN_RIGHT[$this->runner->direction];
                        $steps[] = $this->map->renderAsDiv($this->runner);
                        $stop = ($count++ > $this->map->size);
                    }
                    break;
                case Constants::PLAN_GO :
                    (Messages::getInstance())->addMessage($this->trans->say(Constants::ERR_HIT_WALL));
                    $this->runner->hitWall = TRUE;
                    break;
                default :
                    $good_cmd = FALSE;
            }
        } elseif ($condition === Constants::PLAN_NOT_SEE_WALL) {
            switch ($action) {
                case Constants::PLAN_LEFT :
                    while (!$this->map->seeWall($this->runner) && !$stop) {
                        $this->runner->direction = Constants::PLAN_TURN_LEFT[$this->runner->direction];
                        $steps[] = $this->map->renderAsDiv($this->runner);
                        $stop = ($count++ > $this->map->size);
                    }
                    break;
                case Constants::PLAN_RIGHT :
                    while (!$this->map->seeWall($this->runner) && !$stop) {
                        $this->runner->direction = Constants::PLAN_TURN_RIGHT[$this->runner->direction];
                        $steps[] = $this->map->renderAsDiv($this->runner);
                        $stop = ($count++ > $this->map->size);
                    }
                    break;
                case Constants::PLAN_GO :
                    while (!$this->map->seeWall($this->runner)) {
                        $this->map->moveRunner($this->runner);
                        $steps[] = $this->map->renderAsDiv($this->runner);
                    }
                    break;
                default :
                    $good_cmd = FALSE;
            }
        } else {
            $good_cmd = FALSE;
        }
        if (!$good_cmd) {
            (Messages::getInstance())->addMessage($this->trans->say(Constants::ERR_CMD));
            (Messages::getInstance())->addMessage('WHILE' . ' ' . implode(' ', $args));
        }
        return $count;
    }
    /**
     * Exercises these possible instructions:
     *  IF NOT_SEE_WALL GO
     *  IF NOT_SEE_WALL LEFT
     *  If NOT_SEE_WALL RIGHT
     *  IF SEE_WALL GO
     *  IF SEE_WALL LEFT
     *  IF SEE_WALL RIGHT
     *
     * Note that the "IF SEE_WALL GO" instruction results in an error!
     * But we allow this as part of the learning process
     *
     * @param array $args
     * @param array $steps
     * @return string $html
     */
    public function planIf(array $args, array &$steps)
    {
        $good_cmd  = TRUE;
        $condition = $this->getArg($args, 0);
        $action    = $this->getArg($args, 1);
        if ($condition === Constants::PLAN_SEE_WALL) {
            if ($this->map->seeWall($this->runner)) {
                switch ($action) {
                    case Constants::PLAN_GO :
                        $this->map->moveRunner($this->runner);
                        break;
                    case Constants::PLAN_LEFT :
                        $this->runner->direction = Constants::PLAN_TURN_LEFT[$this->runner->direction];
                        break;
                    case Constants::PLAN_RIGHT :
                        $this->runner->direction = Constants::PLAN_TURN_RIGHT[$this->runner->direction];
                        break;
                    default :
                        $good_cmd = FALSE;
                }
            }
        } elseif ($condition === Constants::PLAN_NOT_SEE_WALL) {
            if (!$this->map->seeWall($this->runner)) {
                switch ($action) {
                    case Constants::PLAN_GO :
                        $this->map->moveRunner($this->runner);
                        break;
                    case Constants::PLAN_LEFT :
                        $this->runner->direction = Constants::PLAN_TURN_LEFT[$this->runner->direction];
                        break;
                    case Constants::PLAN_RIGHT :
                        $this->runner->direction = Constants::PLAN_TURN_RIGHT[$this->runner->direction];
                        break;
                    default :
                        $good_cmd = FALSE;
                }
            }
        } else {
            $good_cmd = FALSE;
        }
        if ($good_cmd) {
            $steps[] = $this->map->renderAsDiv($this->runner);
        } else {
            (Messages::getInstance())->addMessage($this->trans->say(Constants::ERR_CMD));
            (Messages::getInstance())->addMessage('IF' . ' ' . implode(' ', $args));
        }
        return (int) $good_cmd;
    }
    /**
     * Exercises these possible instructions:
     *  FOR NN GO
     *  FOR NN  LEFT
     *  FOR NN  RIGHT
     *
     * @param array $args
     * @param array $steps
     * @return array $steps
     */
    public function planFor(array $args, array &$steps)
    {
        $good_cmd = TRUE;
        $count    = 0;
        $num      = (int) ($args[0] ?? 0);
        $action    = $this->getArg($args, 1);
        switch ($action) {
            case Constants::PLAN_LEFT :
                for ($x = 0; $x < $num; $x++) {
                    $this->runner->direction = Constants::PLAN_TURN_LEFT[$this->runner->direction];
                    $steps[] = $this->map->renderAsDiv($this->runner);
                    $count++;
                }
                break;
            case Constants::PLAN_RIGHT :
                for ($x = 0; $x < $num; $x++) {
                    $this->runner->direction = Constants::PLAN_TURN_RIGHT[$this->runner->direction];
                    $steps[] = $this->map->renderAsDiv($this->runner);
                    $count++;
                }
                break;
            case Constants::PLAN_GO :
                for ($x = 0; $x < $num; $x++) {
                    $this->map->moveRunner($this->runner);
                    $steps[] = $this->map->renderAsDiv($this->runner);
                    $count++;
                    if (!$this->checkStop($this->runner)) break;
                }
                break;
            default :
                $good_cmd = FALSE;
        }
        if (!$good_cmd || $num === 0) {
            (Messages::getInstance())->addMessage($this->trans->say(Constants::ERR_CMD));
            (Messages::getInstance())->addMessage('FOR' . ' ' . implode(' ', $args));
        }
        return $count;
    }
}

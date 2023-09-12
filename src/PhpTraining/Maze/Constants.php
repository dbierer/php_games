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

class Constants
{
    const NORTH          = 'north';
    const SOUTH          = 'south';
    const EAST           = 'east';
    const WEST           = 'west';
    const GRID           = 'grid';
    const PROCESS        = 'process';
    const AJAX_URL       = '/training/maze';
    const TRAIL_COLOR    = 'purple';
    const TYPE_NORMAL    = 'NORMAL';
    const TYPE_WALL      = 'WALL';
    const TYPE_TRAIL     = 'TRAIL';
    const TYPE_RUNNER    = 'RUNNER';
    const INIT_GRID      = 'init_grid';
    const SOUND_STEP     = 'SOUND_STEP';
    const SOUND_WALL     = 'SOUND_HIT_WALL';
    const SOUND_DONE     = 'SOUND_DONE';
    const DEF_LANG       = 'EN';
    const DEF_RUNNER     = 'JR';
    const DEF_NAME       = '12';
    const DEF_GRID_SIZE    = 16;
    const DEF_SQ_TAG       = 'div';
    const DEF_SQ_CLASS     = 'col square';
    const DEF_SQ_COLOR     = 'white';
    const DEF_SQ_SIZE      = 150;
    const DEF_MAP_NAME     = 'Blank';
    const DEF_MAP_RENDER   = 'div';    // choices: table|div|json
    const DEF_MAP_START    = [2,0];
    const DEF_MAP_WINNER   = [self::DEF_GRID_SIZE - 2,self::DEF_GRID_SIZE];
    const DEF_AVATAR_CLASS = 'runner_avatar';
    const DEF_NML_CONTENT  = '<img src="/images/normal.png" class="std-img"/>';
    const DEF_WALL_CONTENT = '<img src="/images/wall.png" class="std-img"/>';
    const DEF_TRAIL_CONTENT = '<img src="/images/trail.png" class="std-img"/>';
    const DEF_RUNNER_CONTENT = '<img class="%s" src="/images/avatar/%s"/>';
    const DEF_DOORS = [
        self::SOUTH => [
            1 => 0,
            2 => 0,
            3 => 0
        ],
        self::NORTH => [
            self::DEF_GRID_SIZE - 1 => self::DEF_GRID_SIZE,
            self::DEF_GRID_SIZE - 2 => self::DEF_GRID_SIZE,
            self::DEF_GRID_SIZE - 3 => self::DEF_GRID_SIZE,
        ],
    ];
    const DEF_INNER  = [
        [1,6],[2,6],[3,6],[4,6],[5,6],[6,6],[7,6],[8,6],[9,6],[10,6],[11,6],
        [4,11],[5,11],[6,11],[7,11],[8,11],[9,11],[10,11],[11,11],[12,11],[13,11],[14,11],[15,11],
    ];
    const DEF_PLAN   = ['WHILE NOT_SEE_WALL GO'];
    const DEF_SND_STEP = '/sounds/step.mp3';
    const DEF_SND_WALL = ['/sounds/hit_wall_1.mp3','/sounds/hit_wall_2.mp3','/sounds/hit_wall_3.mp3',];
    const DEF_SND_DONE = '/sounds/winner.mp3';
    const DEF_MAP_CONFIG = [
        'name'   => 'Default',
        'size'   => 16,
        'doors'  => [
            self::SOUTH => [[1,0],[2,0],[3,0]],
            self::NORTH => [[13,16],[14,16],[15,16]],
        ],
        'start'  => self::DEF_MAP_START,   // start = South door
        'winner' => [[13,16],[14,16],[15,16]],  // winner = North door
        'inner'  => self::DEF_INNER,
        self::SOUND_STEP => '/sounds/step.mp3',
        self::SOUND_WALL => '/sounds/hit_wall.mp3',
    ];
    const CLEAR      = 'clear';     // next square is normal
    const SEE_WALL   = 'see_wall';  // next square is a wall
    const WINNER     = 'winner';    // runner is at the exit gate
    const PLAN_FN    = 'FN';
    const PLAN_GO    = 'GO';
    const PLAN_LEFT  = 'LEFT';
    const PLAN_RIGHT = 'RIGHT';
    const PLAN_IF    = 'IF';
    const PLAN_FOR   = 'FOR';
    const PLAN_WHILE = 'WHILE';
    const PLAN_SEE_WALL = 'SEE_WALL';
    const PLAN_NOT_SEE_WALL = 'NOT_SEE_WALL';
    const PLAN_TURN_LEFT = [
        self::NORTH => self::WEST,
        self::WEST  => self::SOUTH,
        self::SOUTH => self::EAST,
        self::EAST  => self::NORTH,
    ];
    const PLAN_TURN_RIGHT = [
        self::NORTH => self::EAST,
        self::EAST  => self::SOUTH,
        self::SOUTH => self::WEST,
        self::WEST  => self::NORTH,
    ];
    const ERR_PLAN     = 'NO_MAP_NO_RUNNER';
    const ERR_HIT_WALL = 'HIT_WALL';
    const ERR_OFF_GRID = 'OFF_GRID';
    const ERR_CMD      = 'UNRECOGNIZED_CMD';
    const ERR_ERR      = 'UNKNOWN_ERROR';
    const ERR_READY    = 'NOT_READY';
    const FORM_LANG   = 'Language';
    const FORM_PLAN   = 'Enter Plan';
    const FORM_SUBMIT = 'Submit';
    const FORM_RESET  = 'Reset';
    const FORM_RUN    = 'Run';
    const FORM_INS    = 'Directions';
    const FORM_AUTO   = 'Auto';
    const FORM_CMDS   = 'Commands';
    const FORM_CMDS_LONG = 'Commands Long Version';
}

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

class Runner extends Base implements RunnerInterface
{
    public $avatar = '';
    public $id = 0;
    public $name = '';
    public $trail = [];
    public $config = [];
    public $language = 'EN';
    public $direction = Constants::NORTH;
    public $translate = NULL;
    public $avatar_class = Constants::DEF_AVATAR_CLASS;
    public $x = 2;  // current "X" coordinate of runner
    public $y = 0;  // current "Y" coordinate of runner
    public $hitWall = FALSE;
    public $winner  = FALSE;
    public function init(array $args)
    {
        $this->avatar = $args['avatar'] ?? '';
        $this->id     = $args['id'] ?? 0;
        $this->name   = $args['name'] ?? '';
        $this->trail  = $args['trail'] ?? [];
        $this->content = $args['content'] ?? Constants::DEF_RUNNER_CONTENT;
        $this->language = $args['language'] ?? 'EN';
        $this->translate = new Translate($this->config, ['lang' => $this->language]);
        $this->direction = $args['direction'] ?? Constants::NORTH;
        $this->avatar_class = $args['avatar_class'] ?? Constants::DEF_AVATAR_CLASS;
        $this->x = $args['x'] ?? 2;
        $this->y = $args['y'] ?? 0;
        $this->config = $this->config['PHP_TRAINING']['maze']['runner'];
    }
    public function getContent() : string
    {
        return sprintf($this->content,$this->avatar_class,basename($this->avatar));
    }
}

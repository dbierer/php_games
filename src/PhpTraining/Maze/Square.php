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

class Square extends Base
{
    public $type  = Constants::TYPE_NORMAL;
    public $x     = 0;  // "X" coordinate
    public $y     = 0;  // "Y" coordinate
    public $css   = [];
    public $tag   = Constants::DEF_SQ_TAG;
    public $content = Constants::DEF_NML_CONTENT;
    public function init(array $args)
    {
         $this->type      = $args['type']  ?? Constants::TYPE_NORMAL;
         $this->tag       = $args['tag']   ?? Constants::DEF_SQ_TAG;
         $this->content   = $args['content'] ?? Constants::DEF_NML_CONTENT;
         $this->css_class = $args['css_class'] ?? Constants::DEF_SQ_CLASS;
         $this->x = $args['x'] ?? 0;
         $this->y = $args['y'] ?? 0;
    }
    public function getId() : string
    {
        return $this->x . '_' . $this->y;
    }
    public function renderAsHtml()
    {
        $html = '<' . $this->tag . ' ';
        // add ID
        $html .= 'id="' . $this->getId() . '" ';
        // add CSS
        $html .= 'class="fill square ' . $this->css_class . '" ';
        $html .= '>';
        // add content
        $html .= $this->content;
        // close tag
        $html .= '</' . $this->tag . '>';
        return $html;
    }
}

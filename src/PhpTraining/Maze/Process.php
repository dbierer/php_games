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

use FileCMS\Common\Generic\Messages;
class Process
{
    public function clear()
    {
        $_SESSION[Constants::PROCESS] = [];
    }
    public function push(string $html)
    {
        if (empty($_SESSION[Constants::PROCESS])) {
            $_SESSION[Constants::PROCESS][] = base64_encode($html);
        } else {
            array_unshift($_SESSION[Constants::PROCESS], base64_encode($html));
        }
        return $html;
    }
    public function pop()
    {
        return (!empty($_SESSION[Constants::PROCESS]))
                ? base64_decode(array_pop($_SESSION[Constants::PROCESS]))
                : '';
    }
    public function pull(int $idx)
    {
        return (!empty($_SESSION[Constants::PROCESS][$idx]))
                ? base64_decode($_SESSION[Constants::PROCESS][$idx])
                : '';
    }
    public function hasSteps()
    {
        return (bool) $this->count();
    }
    public function count()
    {
        return (!empty($_SESSION[Constants::PROCESS]))
               ? count($_SESSION[Constants::PROCESS])
               : 0;
    }
    // NOTE: formatted for jQuery jcarousel
    // See: https://github.com/jsor/jcarousel/blob/master/examples/basic/index.html
    public function getStepsAsHtml()
    {
        if (!empty($_SESSION[Constants::PROCESS])) {
            foreach ($_SESSION[Constants::PROCESS] as $html) yield base64_decode($html);
        } else {
            yield '';
        }
    }
}

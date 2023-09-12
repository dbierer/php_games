<?php
// change settings as needed and then copy to /src/config/config.php on your main website
// use this skeleton project to set up your website (https://github.com/dbierer/filecms-website)
// unlikelysource/filecms-core also contains a copy of this file
use PhpTraining\Maze\{Constants,Inner,RenderPlugin};
$inner = include __DIR__ . '/inner.php';
$config = [
    'CARDS'  => 'cards',
    'LAYOUT' => BASE_DIR . '/templates/layout/layout.html',
    'HOME'   => 'home.phtml',   // default home page
    'HOST'   => '',
    'DELIM'  => '%%',
    'CONTENTS' => '%%CONTENTS%%',
    'AUTH_DIR' => BASE_DIR . '/logs',
    'CLICK_CSV' => BASE_DIR . '/logs/clicks.csv',
    'MSG_MARKER'  => '<!-- %%MESSAGES%% -->',
    'CONTACT_LOG' => BASE_DIR . '/logs/contact.log',
    // use '' for CACHE if you want to disable it
    'CACHE'  => BASE_DIR . '/data/cache.txt',
    'CAPTCHA' => [
        'input_tag_name' => 'phrase',
        'sess_hash_key'  => 'hash',
        'font_file'      => SRC_DIR . '/fonts/FreeSansBold.ttf',
        'img_dir'        => BASE_DIR . '/public/img/captcha',
        'num_bytes'      => 3,  // each byte == 2 characters
    ],
    'META' => [
        'default' => [
            'title' => 'FileCMS',
            'keywords' => 'php, html, simple',
            'description'  => 'Once installed all you need to do is to upload HTML snippets into the site templates folder',
        ],
    ],
    /*
     * File based storage
     * Can be used if you need persistent storage
     *
     * If "storage_fmt" param === "php" uses native PHP serialization
     * If "storage_fmt" param === "json" uses JSON encoding
     * If "storage_fmt" param === "csv" uses CSV encoding
     */
    'STORAGE' => [
        'storage_fmt' => 'csv',  // can be php|json|csv
        'storage_dir' => BASE_DIR . '/data',
        'storage_fn'  => 'contacts.txt',
    ],
    'PHP_TRAINING' => [
        'maze' => [
            'ajax_url' => '/training/maze',
            'square' => [
                Constants::TYPE_NORMAL => [
                    'type'     => Constants::TYPE_NORMAL,
                    'tag'      => 'div',
                    'content'  => Constants::DEF_NML_CONTENT,
                    'css_class'=> 'col',
                ],
                Constants::TYPE_WALL => [
                    'type'     => Constants::TYPE_WALL,
                    'tag'      => 'div',
                    'content'  => Constants::DEF_WALL_CONTENT,
                    'css_class'=> 'col',
                ],
                Constants::TYPE_TRAIL => [
                    'type'     => Constants::TYPE_TRAIL,
                    'tag'      => 'div',
                    'content'  => Constants::DEF_TRAIL_CONTENT,
                    'css_class'=> 'col',
                ],
            ],
            'maps' => [
                Constants::DEF_MAP_NAME => [
                    'name'   => 'Basic',
                    'size'   => 12,
                    'doors'  => [
                        Constants::SOUTH => [[1,0],[2,0],[3,0]],
                        Constants::NORTH => [[9,12],[10,12],[11,12]],
                    ],
                    'start'  => Constants::DEF_MAP_START,   // start = South door
                    'winner' => [[9,12],[10,12],[11,12]],  // winner = North door
                    'inner'  => $inner[Constants::DEF_NAME](),
                    Constants::SOUND_STEP => Constants::DEF_SND_STEP,
                    Constants::SOUND_DONE => Constants::DEF_SND_DONE,
                    Constants::SOUND_WALL => Constants::DEF_SND_WALL,
                ],
                '15' => [
                    'name'   => 'Fifteen',
                    'size'   => 15,
                    'doors'  => [
                        Constants::SOUTH => [[1,0],[2,0],[3,0]],
                        Constants::NORTH => [[12,15],[13,15],[14,15]],
                    ],
                    'start'  => Constants::DEF_MAP_START,   // start = South door
                    'winner' => [[12,15],[13,15],[14,15]],  // winner = North door
                    'inner'  => $inner['15'](),
                    Constants::SOUND_STEP => Constants::DEF_SND_STEP,
                    Constants::SOUND_DONE => Constants::DEF_SND_DONE,
                    Constants::SOUND_WALL => Constants::DEF_SND_WALL,
                ],
            ],
            'runner' => [
                'JR' => [
                    'x' => 2,
                    'y' => 0,
                    'avatar' => BASE_DIR . '/public/images/avatar/geek.png',
                    'id'     => 101,
                    'name'   => 'JR Jamikorn',
                    'trail'  => [],
                    'content' => Constants::DEF_RUNNER_CONTENT,
                    'language' => 'TH',
                    'direction' => Constants::NORTH,
                    'avatar_class' => 'avatar',
                ],
            ],
            'plan' => [
                'plan' => Constants::DEF_PLAN,
            ],
            'translate' => [
                Constants::DEF_LANG => [
                    Constants::ERR_ERR => 'Unknown Error',
                    Constants::ERR_CMD => 'Bad command',
                    Constants::ERR_PLAN => 'Something wrong with the plan',
                    Constants::ERR_HIT_WALL => 'Oops ... you hit a wall!',
                    Constants::ERR_READY => 'This feature is not yet ready',
                    Constants::PLAN_NOT_SEE_WALL => 'Not See a Wall',
                    Constants::PLAN_SEE_WALL => 'See a Wall',
                    Constants::PLAN_GO => 'Go One Square',
                    Constants::PLAN_LEFT => 'Turn Left',
                    Constants::PLAN_RIGHT => 'Turn Right',
                    Constants::PLAN_WHILE => 'WHILE &lt;CONDITION> &lt;ACTION><br />   Conditions: SEE_WALL|SEE_NO_WALL<br />    Actions: GO|LEFT|RIGHT<br />'
                                             . 'While runner sees, or does not see a wall, take one of these actions: go, turn left, or turn right',
                    Constants::PLAN_FOR => 'FOR &lt;NNN> &lt;ACTION><br />    NNN: Some number<br />    Actions: GO|LEFT|RIGHT<br />'
                                           . 'For NNN times, take one of these actions: go, turn left, or turn right',
                    Constants::FORM_INS => 'Add instructions to the plan to move the runner through the maze.'
                                            . 'Use LEFT or RIGHT to turn the runner.'
                                            . 'Use GO to move one step.'
                                            . 'To move more than one step use FOR or WHILE.'
                                            . 'If the runner hits a wall he stops.'
                                            . 'The runner is unable to go off the grid.',
                    Constants::FORM_LANG   => 'Choose Language',
                    Constants::FORM_PLAN   => 'Enter a Plan (see directions)',
                    Constants::FORM_SUBMIT => 'Submit',
                    Constants::FORM_RUN    => 'Run',
                    Constants::FORM_RESET  => 'Reset',
                    Constants::FORM_AUTO   => 'Auto',
                    Constants::FORM_CMDS   => '<b>Commands:</b>'
                                            . '<br />'
                                            . Constants::PLAN_IF . ' &lt;condition> &lt;action>'
                                            . '<br />'
                                            . Constants::PLAN_FOR . ' &lt;number> &lt;action>'
                                            . '<br />'
                                            . Constants::PLAN_WHILE . ' &lt;condition> &lt;action>'
                                            . '<br />'
                                            . Constants::PLAN_FN . ' &lt;name> (&lt;arg>) {&lt;commands>}'
                                            . '<br />'
                                            . '<b>Actions:</b>'
                                            . '<br />'
                                            . Constants::PLAN_GO . ' | ' . Constants::PLAN_LEFT . ' | ' . Constants::PLAN_RIGHT
                                            . '<br />'
                                            . '<b>Conditions:</b>'
                                            . '<br />'
                                            . Constants::PLAN_SEE_WALL . ' | ' . Constants::PLAN_NOT_SEE_WALL,
                    Constants::FORM_CMDS_LONG   => Constants::PLAN_GO
                                            . '<br/>    Go forward 1 square'
                                            . '<br />'
                                            . Constants::PLAN_LEFT
                                            . '<br/>    Turn left'
                                            . '<br />'
                                            . Constants::PLAN_RIGHT
                                            . '<br/>    Turn right'
                                            . '<br />'
                                            . Constants::PLAN_IF . ' &lt;condition> &lt;action>'
                                            . '<br />    IF &lt;condition> is TRUE, take &lt;action>'
                                            . '<br />    Condition: ' . Constants::PLAN_SEE_WALL . ' | ' . Constants::PLAN_NOT_SEE_WALL
                                            . '<br />    Action   : ' . Constants::PLAN_LEFT . ' | ' . Constants::PLAN_RIGHT . ' | ' . Constants::PLAN_GO
                                            . '<br />'
                                            . Constants::PLAN_FOR . ' &lt;number> &lt;action>'
                                            . '<br />    Take &lt;action> for &lt;number> times'
                                            . '<br />    Number: number of times to take &lt;action>'
                                            . '<br />    Action   : ' . Constants::PLAN_LEFT . ' | ' . Constants::PLAN_RIGHT . ' | ' . Constants::PLAN_GO
                                            . '<br />'
                                            . Constants::PLAN_WHILE . ' &lt;condition> &lt;action>'
                                            . '<br />    Keep doing &lt;action> until &lt;condition> is true'
                                            . '<br />    Condition: ' . Constants::PLAN_SEE_WALL . ' | ' . Constants::PLAN_NOT_SEE_WALL
                                            . '<br />    Action   : ' . Constants::PLAN_LEFT . ' | ' . Constants::PLAN_RIGHT . ' | ' . Constants::PLAN_GO,
                ],
            ],
        ],
    ],
];
return $config;

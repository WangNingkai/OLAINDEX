<?php

return [
    'theme' => (\App\Helpers\Tool::config('theme') === 'mdui' ? 'mdui'
            : 'default').'.',
];

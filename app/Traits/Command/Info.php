<?php

namespace App\Command\Traits;

trait Info
{
    public function info($message = '')
    {
        $this->info($message);
        info($message);
    }
}

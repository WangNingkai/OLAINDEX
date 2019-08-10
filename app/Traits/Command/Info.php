<?php

namespace App\Traits\Command;

trait Info
{
    public function info($message = '')
    {
        $this->info($message);
        info($message);
    }
}

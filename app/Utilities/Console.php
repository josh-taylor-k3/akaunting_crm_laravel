<?php

namespace App\Utilities;

use Symfony\Component\Process\Process;

class Console
{
    public static function run($command)
    {
        $process = new Process($command, base_path());
        $process->setTimeout(900); // 15 minutes

        $process->run();

        if ($process->isSuccessful()) {
            return true;
        }

        return $process->getErrorOutput();
    }
}

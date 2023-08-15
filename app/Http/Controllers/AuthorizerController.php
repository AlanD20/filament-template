<?php

namespace App\Http\Controllers;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AuthorizerController extends Controller
{
    public function authorizer()
    {
        $proc = Process::fromShellCommandline(config('app.clear') . ' ' . base_path());

        try {
            $proc->mustRun();
            echo $proc->getOutput();
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
        }
    }
}

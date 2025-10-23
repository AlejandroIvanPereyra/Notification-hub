<?php

namespace App\Providers;

use App\Models\MessageTarget;

interface MessageProviderInterface
{
    public function send(MessageTarget $target): bool;
}
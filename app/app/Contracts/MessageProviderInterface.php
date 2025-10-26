<?php

namespace App\Contracts;

use App\Models\MessageTarget;

interface MessageProviderInterface
{
    public function send(MessageTarget $target): bool;
}
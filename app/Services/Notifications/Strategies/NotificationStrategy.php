<?php

namespace App\Services\Notifications\Strategies;

interface NotificationStrategy
{
    public function send(string $phone, string $message): bool;
}

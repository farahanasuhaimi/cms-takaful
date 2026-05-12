<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Client;

class ClientObserver
{
    public function created(Client $client): void
    {
        ActivityLog::record('client.created', "Added client {$client->name}", 'client', $client->id);
    }

    public function updated(Client $client): void
    {
        ActivityLog::record('client.updated', "Updated client {$client->name}", 'client', $client->id);
    }

    public function deleted(Client $client): void
    {
        ActivityLog::record('client.deleted', "Removed client {$client->name}");
    }
}

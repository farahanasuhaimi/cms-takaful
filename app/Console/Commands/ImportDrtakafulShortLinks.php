<?php

namespace App\Console\Commands;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Console\Command;

class ImportDrtakafulShortLinks extends Command
{
    protected $signature   = 'short-links:import {--email= : Email of the user to import the links for}';
    protected $description = "Import drtakaful.com's /go/ short links (snapshot of url-map.php) into a user's Short Links";

    public function handle(): int
    {
        $email = $this->option('email');
        $user  = $email
            ? User::where('email', $email)->firstOrFail()
            : User::orderBy('id')->firstOrFail();

        $links = json_decode(file_get_contents(database_path('data/drtakaful-short-links.json')), true);

        $created = 0;
        foreach ($links as $link) {
            // Never overwrite — a code already in the list may carry the user's own notes.
            $row = ShortLink::withoutGlobalScopes()->firstOrCreate(
                ['user_id' => $user->id, 'code' => $link['code']],
                ['section' => $link['section'], 'title' => $link['title'], 'target' => $link['target']],
            );
            $created += $row->wasRecentlyCreated ? 1 : 0;
        }

        $this->info("Imported {$created} new link(s) for {$user->email}; " . (count($links) - $created) . ' already existed.');

        return self::SUCCESS;
    }
}

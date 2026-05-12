<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptExistingData extends Command
{
    protected $signature   = 'tenancy:encrypt';
    protected $description = 'Encrypt plaintext name/phone/ic_no on existing clients and leads (idempotent)';

    public function handle(): int
    {
        $this->encryptTable('clients', ['name', 'phone', 'ic_no']);
        $this->encryptTable('leads',   ['name', 'phone']);

        $this->info('Encryption complete.');

        return self::SUCCESS;
    }

    private function encryptTable(string $table, array $columns): void
    {
        $rows = DB::table($table)->get();
        $count = 0;

        foreach ($rows as $row) {
            $updates = [];

            foreach ($columns as $col) {
                $value = $row->$col;

                if ($value === null) {
                    continue;
                }

                if ($this->isAlreadyEncrypted($value)) {
                    continue;
                }

                $updates[$col] = Crypt::encryptString($value);
            }

            if (! empty($updates)) {
                DB::table($table)->where('id', $row->id)->update($updates);
                $count++;
            }
        }

        $this->line("  {$table}: {$count} rows encrypted");
    }

    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}

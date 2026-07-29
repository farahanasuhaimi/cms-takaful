<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Widen the columns first — raw SQL, since doctrine/dbal (needed by
        // Schema::table()->change()) isn't installed on this project (see
        // 2026_05_12_000023_alter_marketplace_tables_for_strategies.php for
        // the same workaround). Encrypted values run 3-4x longer than
        // plaintext and can overflow the original varchar(255).
        DB::statement('ALTER TABLE quotations MODIFY COLUMN prospect_name TEXT NULL');
        DB::statement('ALTER TABLE quotations MODIFY COLUMN prospect_phone TEXT NULL');
        DB::statement('ALTER TABLE quotation_people MODIFY COLUMN name TEXT NOT NULL');

        // Backfill existing plaintext values via the raw query builder (not
        // Eloquent) so this doesn't depend on — or race against — the
        // 'encrypted' cast being added to the models in this same deploy.
        DB::table('quotations')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('quotations')->where('id', $row->id)->update([
                    'prospect_name'  => $row->prospect_name !== null ? Crypt::encryptString($row->prospect_name) : null,
                    'prospect_phone' => $row->prospect_phone !== null ? Crypt::encryptString($row->prospect_phone) : null,
                ]);
            }
        });

        DB::table('quotation_people')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('quotation_people')->where('id', $row->id)->update([
                    'name' => Crypt::encryptString($row->name),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('quotations')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('quotations')->where('id', $row->id)->update([
                    'prospect_name'  => $row->prospect_name !== null ? Crypt::decryptString($row->prospect_name) : null,
                    'prospect_phone' => $row->prospect_phone !== null ? Crypt::decryptString($row->prospect_phone) : null,
                ]);
            }
        });

        DB::table('quotation_people')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('quotation_people')->where('id', $row->id)->update([
                    'name' => Crypt::decryptString($row->name),
                ]);
            }
        });

        DB::statement("ALTER TABLE quotations MODIFY COLUMN prospect_name VARCHAR(255) NULL");
        DB::statement("ALTER TABLE quotations MODIFY COLUMN prospect_phone VARCHAR(255) NULL");
        DB::statement("ALTER TABLE quotation_people MODIFY COLUMN name VARCHAR(255) NOT NULL");
    }
};

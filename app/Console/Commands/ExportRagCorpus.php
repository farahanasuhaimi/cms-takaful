<?php

namespace App\Console\Commands;

use App\Models\PlanProduct;
use Illuminate\Console\Command;

class ExportRagCorpus extends Command
{
    protected $signature = 'export:rag-corpus {--output=rag-corpus.json : Path to write the JSON export to}';
    protected $description = 'Export the plan_products catalog as flat JSON for the takaful-reply-extension RAG corpus';

    public function handle(): int
    {
        $products = PlanProduct::withoutGlobalScopes()->get()->map(fn ($p) => [
            'id'         => $p->id,
            'name'       => $p->name,
            'category'   => $p->planTypeLabel(),
            'attributes' => $p->attributes ?? [],
            'notes'      => $p->notes,
        ])->values()->toArray();

        $path = $this->option('output');
        file_put_contents($path, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info(sprintf('Exported %d products to %s', count($products), $path));

        return self::SUCCESS;
    }
}

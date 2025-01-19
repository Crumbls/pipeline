<?php

namespace Crumbls\Pipeline\Stores;

use Crumbls\Pipeline\Contracts\StateStoreInterface;
use Illuminate\Database\ConnectionInterface;

class DatabaseStateStore implements StateStoreInterface
{
    public function __construct(
        private ConnectionInterface $db,
        private string              $table = 'pipeline_states'
    )
    {
    }

    public function save(string $pipelineId, array $state): void
    {
        $this->db->table($this->table)->updateOrInsert(
            ['pipeline_id' => $pipelineId],
            [
                'state' => json_encode($state),
                'updated_at' => now(),
            ]
        );
    }

    public function load(string $pipelineId): ?array
    {
        $record = $this->db->table($this->table)
            ->where('pipeline_id', $pipelineId)
            ->first();

        return $record ? json_decode($record->state, true) : null;
    }

    public function clear(string $pipelineId): void
    {
        $this->db->table($this->table)
            ->where('pipeline_id', $pipelineId)
            ->delete();
    }

    /**
     * Create the pipeline states table if it doesn't exist
     */
    public function createTable(): void
    {
        if (!$this->db->getSchemaBuilder()->hasTable($this->table)) {
            $this->db->getSchemaBuilder()->create($this->table, function ($table) {
                $table->string('pipeline_id')->primary();
                $table->json('state');
                $table->timestamps();
            });
        }
    }
}

<?php

namespace Crumbls\Pipeline\State;

class DatabaseStateStore implements StateStoreInterface
{
    public function __construct(
        private \Illuminate\Database\ConnectionInterface $db,
        private string $table = 'pipeline_states'
    ) {}

    public function save(string $pipelineId, array $state): void
    {
        $this->db->table($this->table)->updateOrInsert(
            ['pipeline_id' => $pipelineId],
            [
                'state' => json_encode($state),
                'updated_at' => now()
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
}

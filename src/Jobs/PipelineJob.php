<?php

use Crumbls\Pipeline\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
/**
 * PipelineJob handles the execution of the pipeline in the background
 */
class PipelineJob implements ShouldQueue
{
    use Queueable,
        InteractsWithQueue,
        SerializesModels;

    protected $pipeline;
    protected $passable;

    public function __construct(StatefulPipeline $pipeline, $passable)
    {
        $this->pipeline = $pipeline;
        $this->passable = $passable;
    }

    public function handle()
    {
        return $this->pipeline->thenReturn($this->passable);
    }
}

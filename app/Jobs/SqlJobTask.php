<?php

namespace App\Jobs;

use App\Http\Controllers\SqlJobs\SqlJobService;
use App\Models\SqlJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Carbon;

class SqlJobTask implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sqlJob;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SqlJob $sqlJob)
    {
      $this->sqlJob = $sqlJob;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

      // Validate if SqlJob was not cancelled
      if( $this->sqlJob->status != SqlJob::STATUS_PENDING ) {
        $this->message($this->sqlJob, $this->sqlJob->status);
        return;
      }

      // Validate if SqlJob execution_date was not chenged
      if( $this->wasDelayed($this->sqlJob) ) {
        $this->message($this->sqlJob, 'Delayed');
        return;
      }

      // Run Sccript
      $this->message($this->sqlJob, 'Running');
      SqlJobService::runScript($this->sqlJob);

    }

    public function message($sqlJob, $type = 'Running')
    {
      print_r('______________ Sql Job '. $type .' ______________' . "\n");
      print_r('SqlJob ID: ' . $sqlJob->id . "\n");
      print_r('SqlJob Title: ' . $sqlJob->title . "\n");
      print_r('SqlJob Connection: ' . $sqlJob->connection->name . ', ' . $sqlJob->connection->database . "\n");
      print_r('SqlJob Execution Date: ' . $sqlJob->execution_date . "\n");
      print_r('SqlJob Script: ' . $sqlJob->script . "\n");
      print_r('______________ Sql Job '. $type .' ______________' . "\n");
    }

    public function wasDelayed($sqlJob)
    {
      return Carbon::parse($sqlJob->execution_date)->greaterThan(now());
    }

}

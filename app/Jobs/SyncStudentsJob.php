<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProcessStudentSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $apiYear;
    protected $academicYearId;
    
    public function __construct($apiYear, $academicYearId)
    {
        $this->apiYear = $apiYear;
        $this->academicYearId = $academicYearId;
    }
    
    public function handle()
    {
        // Proses siswa di background
        // Logic yang sama tapi di queue
    }
}
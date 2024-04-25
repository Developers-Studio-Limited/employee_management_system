<?php

namespace App\Console\Commands;

use App\Models\Leave;
use Illuminate\Console\Command;

use function App\errorLogs;

class ApproveUnapprovedLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approve:unapproved_leaves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve unapproved leaves older than 24 hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {   
            $unapprovedLeaves = Leave::where('created_at', '<=', now()->subHours(24))
                                ->where('status', 'pending')                
                                ->get();
    
            foreach ($unapprovedLeaves as $leaves) {
                $leaves->update(['status'=>'approved']);
            }
            $this->info(count($unapprovedLeaves) . ' old unapproved leaves found.');
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }
}

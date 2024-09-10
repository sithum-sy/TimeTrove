<?php

namespace App\Console\Commands;

use App\Models\ServiceRequest;
use App\Mail\ClientReminder;
use App\Mail\ServiceProviderReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendServiceRequestReminders extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-service-request-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to clients and service providers';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $threeDaysBefore = $now->copy()->addDays(3)->format('Y-m-d'); // Clone the current date and add 3 days
        $oneDayBefore = $now->copy()->addDay()->format('Y-m-d'); // Clone the current date and add 1 day

        $requests = ServiceRequest::whereIn('date', [$threeDaysBefore, $oneDayBefore])
            ->where('status', 'confirmed')
            ->get();

        foreach ($requests as $request) {
            if ($request->client && $request->client->email) {
                Mail::to($request->client->email)->send(new ClientReminder($request));
            }

            if ($request->serviceProvider && $request->serviceProvider->email) {
                Mail::to($request->serviceProvider->email)->send(new ServiceProviderReminder($request));
            }
        }
    }
}

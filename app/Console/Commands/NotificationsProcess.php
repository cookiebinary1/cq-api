<?php

namespace App\Console\Commands;

use App\Events\UserNotification;
use App\Models\Collab;
use App\Models\Notification;
use App\Models\User;
use DB;
use Illuminate\Console\Command;
use Log;

class NotificationsProcess extends Command
{
    const SIGNATURE = 'notifications:process';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::SIGNATURE . " {user_id}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process notifications';

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
     * @return array|int
     */
    public function handle(): array|int
    {
        $builder = User::query();
        if ($userId = $this->argument('user_id')) {
            $builder->where('user_id', $userId);
        }

        /** @var User $user */
        foreach (User::cursor() as $user) {
            $user->notificationProcess();
        }
    }
}

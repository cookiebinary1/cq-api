<?php

namespace App\Http\Controllers;

use App\Console\Commands\NotificationsProcess;
use App\Events\UserNotification;
use App\Exceptions\EntityException;
use App\Models\Alias;
use App\Models\Collab;
use App\Models\Creator;
use App\Models\Notification;
use App\Models\User;
use Artisan;
use Carbon\Carbon;
use Database\Seeders\Creators;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use JetBrains\PhpStorm\ArrayShape;
use Log;

/**
 * Class NotificationController
 * @package App\Http\Controllers
 */
class NotificationController extends Controller
{
    /**
     * @return LengthAwarePaginator
     */
    public function notifications(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        // check if .. for sure
        $user->notificationProcess();

        return $user
            ->userNotifications()
            ->with('image', 'entity.creator1.image', 'entity.creator2.image')
            ->orderByDesc('created_at')
            ->paginate();
    }


    public function status()
    {
        Notification::whereIn("id", request("ids"))
            ->update(["status" => request("status")]);

        return ["message" => "ok"];
    }
//    public function pushNotifications()
//    {
//        auth()->user()->notificationProcess();
//        return [];
//    }

    public function total()
    {
        return [
            "total" => auth()->user()->notifications()->where("status", "!=", "clicked")->count()
        ];
    }

    public function markAllAsClicked()
    {
        auth()->user()->notifications()->update(['status' => 'clicked']);

        return ['status' => 'ok'];
    }
}

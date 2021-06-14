<?php

use App\Http\Controllers\AliasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollabController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ExistingCollabController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\UserController;
use BeyondCode\LaravelWebSockets\Dashboard\Http\Controllers\AuthenticateDashboard;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {

    $auth = AuthController::class;
    $profile = ProfileController::class;
    $user = UserController::class;
    $common = CommonController::class;
    $creator = CreatorController::class;
    $category = CategoryController::class;
    $source = SourceController::class;
    $collab = CollabController::class;
    $country = CountryController::class;
    $comment = CommentController::class;
    $notification = NotificationController::class;
    $existingCollab = ExistingCollabController::class;

    Route::group(
        ['middleware' => ['cors', 'json.response']],
        function () use (
            $auth, $profile, $creator, $category, $collab, $country, $source, $common, $comment, $user, $notification, $existingCollab,
        ) {
            // auth
            Route::post('/auth/register', $auth . '@register')->name('auth.register');
            Route::post('/auth/login', $auth . '@login')->name('auth.login');
            Route::post('/auth/two-factor/login', $auth . '@twoFactorLogin')->name('auth.twoFactorLogin');
            Route::post('/auth/verify', $auth . '@verify')->name('auth.verify');

            // profile
            Route::get('/me', $profile . '@me')->name('auth.me');
            Route::post('/me/password/request', $profile . '@passwordChangeRequest')->name('auth.password.change.request');
            Route::post('/me/password', $profile . '@passwordChange')->name('auth.password.change');

            // user
            Route::get('/user', $user)->name('user');
            Route::get('/user/search', $user . "@search")->name('user.search');

            // search
            Route::get('/common/search', $common . '@search')->name('common.search');

            // entities:
            Route::get('/creator/search', $creator . '@search')->name('creator.search');
            Route::get('/category', $category . '@index')->name('category.index');
            Route::get('/source', $source . '@index')->name('source.index');

            Route::get('/creator/detail/', $creator . '@detail')->name('creator.detail');
//            Route::get('/creator/id/', $creator . '@id')->name('creator.id');
            Route::get('/creator/', $creator . '@index')->name('creator.index');

            Route::get('/collab/detail/', $collab . '@detail')->name('collab.detail');
//            Route::get('/collab/id/', $collab . '@id')->name('collab.id');
            Route::get('/collab', $collab . '@index')->name('collab.index');
            Route::get('/collab/by-creators', $collab . '@byCreators')->name('collab.byCreators');

            Route::get('/country', $country . '@index')->name('country.index');

            Route::get('/entity/id', $common . '@id')->name('common.entity.id');

            Route::get('/comment', $comment . '@index')->name('comment.index');

            Route::get('/common/slugs', $common . '@slugs')->name('common.slugs');

            Route::get('/common/alias', $common . "@alias")->name('alias.detail');

            Route::get('/existing-collab', $existingCollab . "@index")->name('existingCollab.index');

            Route::middleware('auth:api')->group(function () use (
                $common, $auth, $profile, $creator, $category, $collab, $comment, $notification, $existingCollab,
            ) {
                // notifications
                Route::get('/notifications', $notification . '@notifications')->name('notifications');
                Route::post('/notifications/status', $notification . '@status')->name('notifications.status');
                Route::get('/notifications/total', $notification . '@total')->name('notifications.total');
                Route::post('/notifications/mark-all-as-clicked', $notification . '@markAllAsClicked')->name('notifications.markAllAsClicked');

                // auth
                Route::delete('/auth/logout', $auth . '@logout')->name('auth.logout');

                // profile
                Route::post('/me', $profile . '@saveProfile')->name('auth.profile.save');
                Route::post('/me/image', $profile . '@image')->name('auth.profile.image');
                Route::delete('/me/image', $profile . '@deleteImage')->name('auth.profile.image.delete');

                // creator
                Route::post('/creator/create', $creator . '@create')->name('creator.create');
                Route::post('/category/create', $category . '@create')->name('category.create');
                Route::post('/creator/avatar', $creator . '@avatar')->name('creator.avatar')->middleware('cloudinary');
                Route::post('/creator/url', $creator . '@url')->name('creator.url');
                Route::post('/creator/like', $creator . '@createLike')->name('creator.like');
                Route::delete('/creator/like', $creator . '@deleteLike')->name('creator.like');


                // collab
                Route::post('/collab/create', $collab . '@create')->name('collab.create');
                Route::delete('/collab/delete', $collab . '@delete')->name('collab.delete');
                Route::post('/collab/like', $collab . '@createLike')->name('collab.like');
                Route::delete('/collab/like', $collab . '@deleteLike')->name('collab.like');
                Route::put('/collab/status', $collab . '@status')->name('collab.status');

                // comments
                Route::post('/comment', $comment . '@create')->name('comment.create');
                Route::post('/comment/like', $comment . '@createLike')->name('comment.like');
                Route::delete('/comment/like', $comment . '@deleteLike')->name('comment.like');

                // ws
                Route::post('websockets/auth', AuthenticateDashboard::class); //, 'BeyondCode\LaravelWebSockets\Dashboard\Http\Controllers\AuthenticateDashboard');

                Route::post('/existing-collab', $existingCollab . "@post")->name('existingCollab.post');
            });
        });
});

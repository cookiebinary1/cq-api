<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityException;
use App\Exceptions\ErrorException;
use App\Models\Alias;
use App\Models\Image;
use Exception;
use FusionAuth\ClientResponse;
use FusionAuth\FusionAuthClient;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Class ApiAuthController
 * @package App\Http\Controllers\Auth
 * @author  Cookie
 */
class ProfileController extends Controller
{
    private FusionAuthClient $authClient;

    /**
     * AuthController constructor.
     * @param FusionAuthClient $authClient
     */
    public function __construct(FusionAuthClient $authClient)
    {
        $this->authClient = $authClient;
    }

    /**
     * @return array
     */
    #[ArrayShape(['currentUser' => "mixed"])]
    public function me(): array
    {
        return [
            'currentUser' => auth()
                ->guard('api')
                ->user() // could be null if user not logged in
                ?->makeHidden('fusion_data')
                ?->load('image')
                ?->load('country')
        ];
    }

    /**
     * @return Model|Collection|array|User|null
     */
    #[ArrayShape(['currentUser' => "mixed"])]
    public function saveProfile(): Model|Collection|array|User|null
    {
        /** @var User $user */
        $user = auth()->user();
        $originalSlug = $user->slug;

        $user->slug = null;

        $user->update([
            'name'       => request('name'),
            'country_id' => request('countryId'),
        ]);

        if ($originalSlug != $user->slug) {
            Alias::create([
                'entity'        => 'User',
                'original_slug' => $originalSlug,
                'current_slug'  => $user->slug,
            ]);
        }

        return $this->me();
    }

    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory|Image
     */
    public function image(Request $request): Response|Application|ResponseFactory|Image
    {
        $user = auth()->user();

        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {

                $request->validate([
                    'name'  => 'string',
                    'image' => 'mimes:jpeg,png|max:10000',
                ]);

                $user->image()->associate(Image::create([
                    'url'  => $request->file('image')->path(),
                    'data' => 'N/A',
                    'type' => 'user',
                ])->uploadImagekit());

                $user->save();

                return $user->image;
            }
        } else if ($url = request('url')) {
            auth()->user()->image()->associate(Image::create([
                'url'  => request('url'),
                'data' => 'N/A',
                'type' => 'user',
            ]));
            auth()->user()->save();
            return auth()->user()->image;
        }

        return error(20200); // Image upload error.
    }

    /**
     * @return string[]
     * @throws ErrorException
     * @throws Exception
     */
    #[ArrayShape(["message" => "string"])]
    public function deleteImage(): array
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var Image $image */
        $image = auth()->user()->image;

        if (!$image)
            throw new ErrorException(20201);

        // disconnect image from user first
        $user->image()->disassociate();
        $user->save();

        // then delete the image
        $image->delete();

        return ["message" => "success"];
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function passwordChangeRequest(): Response|Application|ResponseFactory
    {
        return response(null);
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function passwordChange(): Response|Application|ResponseFactory
    {
        return response(null);
    }
}

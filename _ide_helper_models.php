<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Class Alias
 *
 * @package App\Models
 * @property int $id
 * @property string $entity
 * @property string $original_slug
 * @property string $current_slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Alias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alias query()
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereCurrentSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereOriginalSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereUpdatedAt($value)
 */
	class Alias extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Category
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int|null $created_by_id
 * @property string $name
 * @property string|null $slug
 * @property string $description
 * @property int $generic
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Creator[] $creators
 * @property-read int|null $creators_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereGeneric($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Collab
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property string|null $slug
 * @property int $user_id
 * @property int $creator1_id
 * @property int $creator2_id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property-read int|null $likes_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Creator $creator1
 * @property-read \App\Models\Creator $creator2
 * @property-read string $full_title
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \App\Models\Notification|null $notification
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $userLikes
 * @property-read int|null $user_likes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Collab findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collab newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collab query()
 * @method static \Illuminate\Database\Eloquent\Builder|Collab search(string $query, $limit = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereCreator1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereCreator2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collab withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Collab extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Comment
 *
 * @package App\Models
 * @author Cookie
 * @method static create(array $array)
 * @property int $id
 * @property int $commentable_id
 * @property string $commentable_type
 * @property int $user_id
 * @property string $text
 * @property string|null $type
 * @property int|null $reply_to_id
 * @property string $status
 * @property-read int|null $likes_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CommentLike[] $likes
 * @property-read \App\Models\Notification|null $notification
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CommentLike[] $userLikes
 * @property-read int|null $user_likes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Comment initial()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment mine()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereReplyToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 */
	class Comment extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Like
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int $user_id
 * @property int $comment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Comment $comment
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereUserId($value)
 */
	class CommentLike extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Country
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int $num_code
 * @property string|null $alpha_2_code
 * @property string|null $alpha_3_code
 * @property string|null $en_short_name
 * @property string|null $nationality
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Creator[] $creators
 * @property-read int|null $creators_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereAlpha2Code($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereAlpha3Code($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereEnShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNumCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Creator
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property int|null $created_by_id
 * @property int|null $user_id
 * @property string $status
 * @property int|null $country_id
 * @property int|null $image_id
 * @property int $priority
 * @property-read int|null $likes_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Collab[] $collabs1
 * @property-read int|null $collabs1_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Collab[] $collabs2
 * @property-read int|null $collabs2_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CreatorInfo[] $info
 * @property-read int|null $info_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CreatorLike[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Source[] $sources
 * @property-read int|null $sources_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CreatorLike[] $userLikes
 * @property-read int|null $user_likes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Creator findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Creator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Creator query()
 * @method static \Illuminate\Database\Eloquent\Builder|Creator search(string $query, $limit = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Creator withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Creator extends \Eloquent {}
}

namespace App\Models{
/**
 * Class CreatorInfo
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int $creator_id
 * @property string $field
 * @property string|null $value
 * @property int|null $source_id
 * @property int $generic
 * @property int $visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Creator $creator
 * @property-read \App\Models\Source|null $source
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereGeneric($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorInfo whereVisible($value)
 */
	class CreatorInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * Class CreatorLike
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int $user_id
 * @property int $creator_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Creator $creator
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreatorLike whereUserId($value)
 */
	class CreatorLike extends \Eloquent {}
}

namespace App\Models{
/**
 * Class ExistingCollab
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property string|null $title
 * @property int|null $source_id
 * @property string $url
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $collab_id
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereCollabId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExistingCollab whereUrl($value)
 */
	class ExistingCollab extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Image
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property string $type
 * @property string $url
 * @property array|null $data
 * @property array|null $cdn_data
 * @property string $failed
 * @property int $processed
 * @property string|null $original_url
 * @property int|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Image creators()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image processed()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image unprocessed()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCdnData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereFailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereOriginalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUrl($value)
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Like
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int $user_id
 * @property int $collab_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Collab $collab
 * @method static \Illuminate\Database\Eloquent\Builder|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like query()
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCollabId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUserId($value)
 */
	class Like extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Notification
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $entity_type
 * @property int $entity_id
 * @property int $count
 * @property int|null $image_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $entity
 * @property-read \App\Models\Image|null $image
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Source
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property int|null $created_by_id
 * @property string $name
 * @property string|null $slug
 * @property string $description
 * @property string $priority_field
 * @property int $generic
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Creator[] $creators
 * @property-read int|null $creators_count
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder|Source findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Source newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Source newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Source query()
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereGeneric($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source wherePriorityField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Source extends \Eloquent {}
}

namespace App\Models{
/**
 * Class User
 *
 * @package App\Models
 * @author Cookie
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $fusion_id
 * @property array|null $fusion_data
 * @property string|null $remember_token
 * @property int|null $country_id
 * @property int|null $image_id
 * @property string $notified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \App\Models\Country|null $country
 * @property-read mixed $forcename
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notification[] $userNotifications
 * @property-read int|null $user_notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User search(string $query, $limit = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFusionData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFusionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class User extends \Eloquent {}
}


<?php

return [
    1000  => 'OK',
    1001  => 'Undefined error',

    // Auth
    2000  => 'Auth OK',
    2001  => 'Not authorized',
    2002  => 'Authorization failed',
    2003  => "Password mismatch",
    2004  => 'User does not exist',
    2005  => 'Registration failed.',
    2006  => 'Authentication failed.',
    2007  => 'User not verified.',
    2008  => 'Verification failed.',

    // validations
    3001  => 'Request data validation error',

    // creators
    5001  => 'Creator already exists',
    5005  => 'Search query string not received or shorter then 3 characters.',

    // collabs
    6001  => 'Collab already exists',
    6002  => 'Both creators are the same',
    6003  => 'User has liked this collab already',
    6004  => 'Like does not exist',

    // entities commonly
    8001  => "Entity doesn't exists",

    //
    9001  => 'Youtube channel not found',
    9002  => 'Twitch user not found',
    9003  => 'Mixcloud user not found',

    //
    10001 => 'Cannot recognize/process url',

    // comments
    20001 => 'Initial comment exists already.',
    20002 => 'Comment not allowed - permission denied.',
    20003 => 'User has liked this comment already',
    20004 => 'Like (CommentLike) does not exist',

    // creator likes
    20103 => 'User has liked this Creator already',
    20104 => 'Like (CreatorLike) does not exist',

    // image
    20200 => 'Image upload error.',
    20201 => 'There no image to delete.',

    //
    30001 => "User not authenticated or user_id not defined, \"filter\" field requires authentication",

];

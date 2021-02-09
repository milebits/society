<?php

use Milebits\Society\Repositories\CommentsRepository;
use Milebits\Society\Repositories\FriendsRepository;
use Milebits\Society\Repositories\LeansRepository;
use Milebits\Society\Repositories\StoriesRepository;

return [
    'repositories' => [
        'comments' => CommentsRepository::class,
        'friends' => FriendsRepository::class,
        'leans' => LeansRepository::class,
        'stories' => StoriesRepository::class,
    ]
];
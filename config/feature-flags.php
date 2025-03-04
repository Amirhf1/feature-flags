<?php

return [
    'flags' => [
        'new_feature' => [
            'enabled' => true,
            'percentage' => 50,     // Only enable for 50% of users
            'users' => [1, 2, 3],   // Enable only for specific user IDs
        ],
    ],
];

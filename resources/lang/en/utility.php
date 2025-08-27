<?php

return [
    'title' => 'Logger for Statamic',
    'nav_title' => 'Logger',

    'description' => 'View daily action log files.',

    'raw' => 'Raw message',

    'date' => 'Date',
    'download' => 'Download',

    'options' => [
        'show_raw' => 'Show raw message?',
        'show_user_full_details' => 'Show full user details?',
    ],

    'columns' => [
        'date' => 'Date',
        'user' => 'User',
        'type' => 'Type',
        'detail' => 'Details',
    ],
];

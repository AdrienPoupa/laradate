<?php

return [
    // Laradate version
    'VERSION' => '1.0',
    
    // Regex
    'POLL_REGEX' => '/^[a-z0-9-]*$/i',
    'REGEX_POLL_ROUTE' => '^[a-zA-Z0-9-]*$',
    'REGEX_POLL_ADMIN_ROUTE' => '^[a-zA-Z0-9-]{24}$',
    'CHOICE_REGEX' => '/^[X012]$/',
    
    // Session constants
    'SESSION_EDIT_LINK_TIME' => "EditLinkMail",
    
    // Database administrator email
    'ADMIN_MAIL' => 'laradate@laradate.xyz',
    
    // Email for automatic responses (you should set it to "no-reply")
    'NO_REPLY_MAIL' => 'no-reply@laradate.xyz',
    
    // List of supported languages
    'ALLOWED_LANGUAGES' => [
        'fr' => 'Français',
        'en' => 'English',
        'es' => 'Español',
        'de' => 'Deutsch',
        'nl' => 'Dutch',
        'it' => 'Italiano',
    ],
    
    // Path to image file with the title
    'IMAGE_HEADER' => 'images/logo-laradate.png',
    
    // Use REMOTE_USER data provided by web server
    'USE_REMOTE_USER' =>  true,
    
    // Days (after expiration date) before purging a poll
    'PURGE_DELAY' => 60,
    
    // Max slots per poll
    'MAX_SLOTS_PER_POLL' => 366,
    
    // Number of seconds before we allow to resend an "Remember Edit Link" email.
    'TIME_EDIT_LINK_EMAIL' => 60,

    // Editable constants
    'NOT_EDITABLE' => 0,
    'EDITABLE_BY_ALL' => 1,
    'EDITABLE_BY_OWN' => 2,
    
    // Config
    'use_smtp' => true,                     // use email for polls creation/modification/responses notification
    'show_what_is_that' => true,            // display "how to use" section
    'show_the_software' => true,            // display technical information about the software
    'show_cultivate_your_garden' => true,   // display "development and administration" information
    'default_poll_duration' => 180,         // default values for the new poll duration (number of days).
    'user_can_add_img_or_link' => true,     // user can add link or URL when creating his poll.
];
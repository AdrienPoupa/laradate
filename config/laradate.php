<?php

return [
    // Laradate version
    'VERSION' => '1.0',
    
    // Regex
    'POLL_REGEX' => '/^[a-z0-9-]*$/i',
    'ADMIN_POLL_REGEX' => '/^[a-z0-9]{24}$/i',
    'CHOICE_REGEX' => '/^[X012]$/',
    
    // Session constants
    'SESSION_EDIT_LINK_TOKEN' => 'EditLinkToken',
    'SESSION_EDIT_LINK_TIME' => "EditLinkMail",
    
    // Database administrator email
    'ADMIN_MAIL' => 'laradate@laradate.com',
    
    // Email for automatic responses (you should set it to "no-reply")
    'NO_REPLY_MAIL' => 'no-reply@laradate.com',
    
    // List of supported languages, fake constant as arrays can be used as constants only in PHP >=5.6
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
    /* general config */
    'use_smtp' => true,                     // use email for polls creation/modification/responses notification
    /* home */
    'show_what_is_that' => true,            // display "how to use" section
    'show_the_software' => true,            // display technical information about the software
    'show_cultivate_your_garden' => true,   // display "development and administration" information
    /* create_classic_poll.php / create_date_poll.php */
    'default_poll_duration' => 180,         // default values for the new poll duration (number of days).
    /* create_classic_poll.php */
    'user_can_add_img_or_link' => true,     // user can add link or URL when creating his poll.
];
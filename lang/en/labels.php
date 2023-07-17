<?php

return [
    'nav' => [
        'group' => [
            'management' => 'Management',
            'settings' => 'Settings',
            'dev' => 'Developer',
        ],
        'dashboard' => 'Dashboard',
        'user_menu' => [
            'lock_screen' => 'Lock Screen',
            'settings' => 'Account Settings',
        ],
    ],

    'notify' => [
        'approved' => ':Model has been approved. Sender has been notified!',
        'rejected' => ':Model has been rejected. Sender has been notified!',
        'forward' => ':Model has been forwarded. Sender has been notified!',
        'submit' => ':Model has been submitted',
        'add' => ':Model added successfully',
        'create' => ':Model created successfully',
        'edit' => ':Model updated successfully',
        'delete' => ':Model deleted successfully',
        'activate' => 'User account has been activated',
        'deactivate' => 'User account has been deactivated',
        'insufficient_permission' => [
            'title' => 'Insufficient Permissions',
            'body' => 'You don\'t have proper permissions to make this change! Please contact your supervisor.',
        ],
    ],
    'logs' => [
        'notify' => [
            'title' => 'Something went wrong!',
            'message' => 'An error has been occurred. Please inform your supervisor regarding this matter.',
        ],
    ],

    'errors' => [
        'back_to_home' => 'Click go back to home page',
        'default' => [
            'title' => 'Bad Request',
            'page_title' => 'Bad Request!',
            'description' => 'Something went wrong. If this occurs more than usual, plesae inform your supervisor regarding this issue.',
        ],
        '401' => [
            'title' => '401 Unauthorized',
            'page_title' => '401 Unauthorized Access!',
            'description' => 'Login with your credentials to access this page.',
        ],
        '403' => [
            'title' => '403 Forbidden',
            'page_title' => '403 Access is forbidden',
            'description' => 'You don\'t have proper permission to access this page.',
        ],
        '404' => [
            'title' => '404 Not Found',
            'page_title' => '404 Page Not Found!',
            'description' => 'The page you were trying to reach couldn\'t be found. Make sure you have entered a valid URL.',
        ],
        '500' => [
            'title' => '500 Server Error',
            'page_title' => '500 Internal Server Error!',
            'description' => 'The server couldn\'t be reached. Please contact your supervisor regarding this matter.',
        ],
        '503' => [
            'title' => '503 Maintenance',
            'page_title' => '503 Service is Under Maintenance!',
            'description' => 'Please wait until the maintenance is done.',
        ],
    ],

    'report' => [
        'header' => [
            'heading' => 'COMPANY NAME',
            'subheading' => 'DESCRIPTION',
        ],
        'footer' => [
            'address' => 'Address',
            'tel' => 'Tel',
            'email' => 'Email',
            'web' => 'Web',
        ],
    ],
];

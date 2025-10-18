<?php

//require_once __DIR__ . '/vendor/autoload.php';

/**
 * Point d'entrée du thème
 */

add_filter(
    'wp_mail_from',
    function () {
        return 'test@mailhog.local';
    }
);

if (wp_mail('test@example.com', 'MailHog test', 'Hello from WordPress')) {
    echo '<p>email sent !</p>';
} else {
    echo '<p>Error: email not sent !</p>';
}

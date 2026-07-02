<?php
// config/secrets.example.php — template. Copy to secrets.php and fill in real values.
// secrets.php is gitignored and must be created on each environment (local + production).
return [
    'stripe_sk'             => 'sk_live_or_test_...',
    'stripe_webhook_secret' => 'whsec_...',
    'db_user'               => 'db_username',
    'db_pass'               => 'db_password',
    'smtp_user'             => 'noreply@example.com',
    'smtp_pass'             => 'smtp_password',
];

<?php
// Secrets live in config/secrets.php (gitignored). See secrets.example.php.
$__secrets = is_file(__DIR__ . '/secrets.php') ? require __DIR__ . '/secrets.php' : [];

// Publishable key is safe to expose (also used client-side in js/audition-form.js).
define('STRIPE_PK', 'pk_live_51P8mzo2LGldM45CFs1zxLxK6QxmDBfMG84wM2UtbYdgYaZnStMZUOnNQzZuCM7NKtOgA5s9vFqvyIXsPznnvHNEs00FRHnYQiN');
define('STRIPE_SK', $__secrets['stripe_sk'] ?? '');
define('STRIPE_WEBHOOK_SECRET', $__secrets['stripe_webhook_secret'] ?? '');
define('STRIPE_AMOUNT', 100);        // $1  — change to 2500 for $25.00
define('STRIPE_CURRENCY', 'usd');
define('STRIPE_SKIP_PAYMENT', false);        // ← set false when ready to charge

// Dollar string derived from STRIPE_AMOUNT (cents) — use this anywhere the fee is displayed
// so the UI never drifts out of sync with what's actually charged.
function stripe_fee_dollars(): string
{
    return number_format(STRIPE_AMOUNT / 100, 2);
}

// Lightweight Stripe API helper — no SDK required
function stripe_post(string $endpoint, array $params): array
{
    $ch = curl_init('https://api.stripe.com/v1/' . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_USERPWD => STRIPE_SK . ':',
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 15,
    ]);
    $body = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curl_errno) {
        error_log("Stripe cURL error [$endpoint]: ($curl_errno) $curl_error");
        return [];
    }

    $decoded = json_decode($body ?: '{}', true) ?: [];
    if ($http_code >= 400) {
        error_log("Stripe API error [$endpoint] HTTP $http_code: " . ($body ?: '(empty body)'));
    }
    return $decoded;
}

function stripe_get(string $endpoint): array
{
    $ch = curl_init('https://api.stripe.com/v1/' . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => STRIPE_SK . ':',
        CURLOPT_TIMEOUT => 15,
    ]);
    $body = curl_exec($ch);
    curl_close($ch);
    return json_decode($body ?: '{}', true) ?: [];
}

function verify_stripe_signature(string $payload, string $sig_header, string $secret): bool
{
    $parts = [];
    foreach (explode(',', $sig_header) as $item) {
        [$k, $v] = array_pad(explode('=', $item, 2), 2, '');
        $parts[$k][] = $v;
    }
    $timestamp = $parts['t'][0] ?? '';
    $signatures = $parts['v1'] ?? [];
    $expected = hash_hmac('sha256', "$timestamp.$payload", $secret);
    foreach ($signatures as $sig) {
        if (hash_equals($expected, $sig))
            return true;
    }
    return false;
}

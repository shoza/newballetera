<?php
// Single source of truth for all navigation links.
// Included automatically by core/render.php.

$NAV_LINKS = [
    ['label' => 'Home',        'path' => '/'],
    ['label' => 'About',       'path' => '/about'],
    ['label' => 'Productions', 'path' => '/productions'],
    [
        'label'    => 'Auditions',
        'path'     => '/auditions',
        'children' => [
            ['label' => 'For Dancers',         'path' => '/dancers'],
            ['label' => 'For Choreographers',  'path' => '/choreographers'],
        ],
    ],
    ['label' => 'Contacts', 'path' => '/contacts'],
];

// Returns the full URL for a nav path, with the correct base prefix.
function nav_url(string $path): string {
    $base = defined('BASE_URL') ? BASE_URL : '';
    // Home: base + '/'  →  /newballetera.com/  or  just  /
    if ($path === '/') return $base . '/';
    return $base . $path;
}

// Returns true if the given nav path matches the current page.
function nav_active(string $path): bool {
    $route = $GLOBALS['current_route'] ?? 'home';
    if ($path === '/') return $route === 'home';
    $segment = ltrim($path, '/');
    return $route === $segment || str_starts_with($route, $segment . '/');
}

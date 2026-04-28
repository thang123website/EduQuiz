<?php

if (!function_exists('is_active_route')) {
    /**
     * Check if current route is active
     *
     * @param string|array $routePatterns
     * @param string $class
     * @return string
     */
    function is_active_route($routePatterns, $class = 'active')
    {
        return request()->routeIs($routePatterns) ? $class : '';
    }
}

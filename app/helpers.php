<?php

// Add your helper functions here.
// Example:
// function exampleHelper() {
//     return 'This is a helper function.';
// }

if (!function_exists('active_class')) {
    function active_class($paths, $class = 'active') {
        foreach ((array) $paths as $path) {
            if (request()->is($path)) {
                return $class;
            }
        }
        return '';
    }
}

if (!function_exists('is_active_route')) {
    function is_active_route($paths) {
        foreach ((array) $paths as $path) {
            if (request()->is($path)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('show_class')) {
    function show_class($paths, $class = 'show') {
        return is_active_route($paths) ? $class : '';
    }
}

<?php

if (! function_exists('org_photo_url')) {
    /**
     * Resolve a member photo path (assets or uploads) to a full URL.
     */
    function org_photo_url(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }

        return base_url(ltrim($path, '/'));
    }
}

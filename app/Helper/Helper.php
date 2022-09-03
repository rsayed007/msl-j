<?php

use Carbon\Carbon;

if (!function_exists('productFilePath')) {
    function productFilePath()
    {
        return 'uploads/' . get_image_prefix_directory_name() . '/';
    }
}

if (!function_exists('get_image_prefix_directory_name')) {
    function get_image_prefix_directory_name(): string
    {
        return Carbon::today()->format('m_Y');
    }
}

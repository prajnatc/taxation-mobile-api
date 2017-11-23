<?php

/*
 * Includes all utility functions availble for the API
 */

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Helper
 * @package    PackageName
 * @author     Vivek <takee.texeira@gmail.com>
 */

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

// Logged in user
if (!function_exists('auth_user')) {
    /**
     * Get the auth_user.
     *
     * @return mixed
     */
    function auth_user()
    {
        return app('Dingo\Api\Auth\Auth')->user();
    }
}

if (!function_exists('dingo_route')) {
    /**
     * 根据别名获得url.
     *
     * @param string $version
     * @param string $name
     * @param string $params
     *
     * @return string
     */
    function dingo_route($version, $name, $params = [])
    {
        return app('Dingo\Api\Routing\UrlGenerator')
            ->version($version)
            ->route($name, $params);
    }
}

if (!function_exists('bcrypt')) {

    /**
     * Encrypts to hash value
     *
     * @param string $string takes string value
     *
     * @return string
     */
    function bcrypt($string)
    {
        return app('hash')->make($string);
    }
}

if (!function_exists('correctnumber')) {

    /**
     * Numbers to be displayed in valid number format
     *
     * @param string $value
     *
     * @return string
     */
    function correctnumber($value = null)
    {

        if ($value < 0) {
            $value = $value * -1;
        }

        $value = trim($value);
        $value = number_format(round($value, 2), 2, '.', '');
        return $value;
    }
}

function count_digit($number)
{

    if (count($number) < 2) {
        $val = 0;

        return $val . $number;
    } else {
        return $number;
    }
}
function decimalHours($time)
{
    $hms = explode(":", $time);
    $timeindecimal = ($hms[0] + ($hms[1] / 60) + ($hms[2] / 3600));
    return number_format(round($timeindecimal, 2), 2, '.', '');
}

function convertTime($decTime)
{
    $hour = floor($decTime);
    $min = round(60 * ($decTime - $hour));
    if ($min > 0) {
        $min = $min;
    } else {
        $min = "00";
    }

    $seconds = "00";
    return count_digit($hour) . ":" . ($min);
}

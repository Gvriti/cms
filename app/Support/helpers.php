<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Events\QueryExecuted;

/**
 * Get the application default language.
 *
 * @return string
 */
function language()
{
    return config('app.language');
}

/**
 * Get the application languages.
 *
 * @param  string|null  $key
 * @return array
 */
function languages($key = null)
{
    if (is_null($key)) {
        return config('app.languages', []);
    }

    return config('app.languages.' . $key);
}

/**
 * Determine if the language is set in the URL.
 *
 * @return bool
 */
function language_isset()
{
    return config('language_isset', false);
}

/**
 * Determine if the CMS routes should be loaded.
 *
 * @return bool
 */
function cms_is_booted()
{
    return config('cms_is_booted', false);
}

/**
 * Get the cms slug
 *
 * @param  string  $path
 * @return string
 */
function cms_slug($path = null)
{
    if (is_null($path)) {
        return config('cms.slug');
    }

    return config('cms.slug') . '/' . $path;
}

/**
 * Get the list of named resource.
 *
 * @param  string  $name
 * @return array
 */
function resource_names($name)
{
    return [
        'index'   => $name . '.index',
        'create'  => $name . '.create',
        'store'   => $name . '.store',
        'show'    => $name . '.show',
        'edit'    => $name . '.edit',
        'update'  => $name . '.update',
        'destroy' => $name . '.destroy'
    ];
}

/**
 * Generate a CMS URL to a named route.
 *
 * @param  string  $name
 * @param  mixed   $parameters
 * @param  string  $language
 * @param  bool    $absolute
 * @return string
 */
function cms_route($name, $parameters = [], $language = null, $absolute = true)
{
    try {
        $route = route($name . '.' . cms_slug(), $parameters, $absolute);
    } catch (Exception $e) {
        return '#not-found';
    }

    return add_language($route, $language);
}

/**
 * Generate a CMS URL.
 *
 * @param  string  $path
 * @param  array   $parameters
 * @param  bool    $secure
 * @return string
 */
function cms_url($path = null, array $parameters = [], $language = null, $secure = null)
{
    return url(prefix_language(cms_slug($path), $language), [], $secure) . build_query($parameters);
}

/**
 * Generate a Site URL to a named route.
 *
 * @param  string  $name
 * @param  mixed   $parameters
 * @param  string  $language
 * @param  bool    $absolute
 * @return string
 */
function site_route($name, $parameters = [], $language = null, $absolute = true)
{
    try {
        $route = route($name, $parameters, $absolute);
    } catch (Exception $e) {
        return '#not-found';
    }

    return add_language($route, $language);
}

/**
 * Generate a Site URL.
 *
 * @param  string  $path
 * @param  array   $parameters
 * @param  string  $language
 * @param  bool    $secure
 * @return string
 */
function site_url($path = null, array $parameters = [], $language = null, $secure = null)
{
    return url(prefix_language($path, $language), [], $secure) . build_query($parameters);
}

/**
 * Build a query string from an array of key value pairs.
 *
 * @param  array  $parameters
 * @param  mixed  $numericPrefix
 * @return string
 */
function build_query(array $parameters, $numericPrefix = null)
{
    if (count($parameters) == 0) {
        return '';
    }

    $query = http_build_query(
        $keyed = empty($numericPrefix) ? Arr::where($parameters, function ($k) {
            return is_string($k);
        }) : $parameters, $numericPrefix
    );

    if (empty($numericPrefix) && count($keyed) < count($parameters)) {
        $query .= '&'.implode(
            '&', Arr::where($parameters, function ($k) {
                return is_numeric($k);
            })
        );
    }

    return '?'.trim($query, '&');
}

/**
 * Prefix language to the path.
 *
 * @param  string  $path
 * @param  string  $language
 * @return string
 */
function prefix_language($path, $language)
{
    if (is_array($path)) {
        $path = implode('/', array_filter($path));
    }

    $path = trim($path, '/');

    if (is_string($language)) {
        $path = $language . '/' . $path;
    } elseif (($language === true || language_isset()) && count(languages()) > 1) {
        $path = language() . '/' . $path;
    }

    return $path;
}

/**
 * Add language to the url.
 *
 * @param  string  $url
 * @param  string  $language
 * @return string
 */
function add_language($url, $language)
{
    $languageList = languages();

    if (! is_null($language) && array_key_exists($language, $languageList)) {
        $segments = parse_url($url);

        if (! isset($segments['path'])) return $url;

        $request = request();

        $path = substr($segments['path'], strlen($request->getBaseUrl()));

        $path = array_filter(explode('/', $path));

        if (array_key_exists(current($path), $languageList)) {
            array_shift($path);
        }

        array_unshift($path, $language);

        $query = isset($segments['query']) ? '?' . $segments['query'] : null;

        return $request->root() . '/' . implode('/', $path) . $query;
    }

    return $url;
}

/**
 * Get the Eloquent model path.
 *
 * @param  string  $name
 * @return string
 */
function model_path($name)
{
    return 'Models\\' . ucfirst(str_singular($name));
}

/**
 * Get the home text, translated for the current language.
 *
 * @return string
 */
function home_text()
{
    return config('site.home.' . language());
}

/**
 * Find specific item(s) into the items tree.
 *
 * @param  array       $items
 * @param  int|string  $value
 * @param  string      $key
 * @param  bool        $multiple
 * @param  bool        $recursive
 * @return mixed
 */
function find_item($items, $value, $key = 'id', $multiple = false, $recursive = true)
{
    if (! is_array($items)) return [];

    $result = [];

    foreach ($items as $item) {
        if (isset($item->$key) && $item->$key == $value) {
            if (! $multiple) return $item;

            $result[] = $item;
        }

        if ($recursive && isset($item->sub)) {
            if ($data = find_item($item->sub, $value, $key, $multiple, $recursive)) {
                $result = is_array($data) ? array_merge($result, $data) : $data;

                if (! $multiple) break;
            }

        }
    }

    return $result;
}

/**
 * Make a nestable items tree.
 *
 * @param  array   $data
 * @param  string  $slug
 * @param  int     $parentId
 * @param  string  $parentKey
 * @param  string  $key
 * @return array
 */
function make_tree($items, $slug = false, $parentId = 0, $parentKey = 'parent_id', $key = 'id')
{
    if (! $items) return [];

    $tree = [];

    $prevSlug = $slug;

    foreach($items as $item) {
        if (isset($item->$parentKey) && $item->$parentKey == $parentId) {
            if ($slug !== false) {
                $slug = $prevSlug ? $prevSlug . '/' . $item->slug : $item->slug;

                $item->original_slug = $item->slug;

                $item->slug = $slug;
            }

            $item->sub = make_tree($items, $slug, $item->$key, $parentKey, $key);

            $tree[] = $item;
        }
    }

    return $tree;
}

/**
 * Get the instance from the container.
 *
 * @param  string  $instance
 * @param  mixed   $default
 * @return array
 */
function app_instance($instance, $default = null)
{
    $app = app();

    if ($app->resolved($instance)) {
        return $app[$instance];
    }

    return $default;
}

/**
 * Fill array with data.
 *
 * @param  string  $result
 * @param  string  $message
 * @param  mixed   $input
 * @return array
 */
function fill_data($result, $message = null, $input = null)
{
    return [
        'result'  => $result,
        'message' => $message,
        'input'   => $input
    ];
}

/**
 * Fill a database error message.
 *
 * @param  string  $key
 * @param  array   $parameters
 * @return array
 */
function fill_db_data($key, array $parameters = [])
{
    return fill_data('error', trans('database.error.' . $key, $parameters));
}

/**
 * Get the CMS User role(s).
 *
 * @param  string  $key
 * @param  mixed   $default
 * @return string|array
 */
function user_roles($key = null, $default = null)
{
    if (! is_null($key)) {
        return config('cms.user_roles.' . $key, $default);
    }

    return config('cms.user_roles', $default);
}

/**
 * Get the pages config.
 *
 * @param  string  $key
 * @param  mixed   $default
 * @return string|array
 */
function cms_pages($key = null, $default = [])
{
    if (! is_null($key)) {
        return config('cms.pages.' . $key, $default);
    }

    return config('cms.pages', $default);
}

/**
 * Get the collections config.
 *
 * @param  string  $key
 * @param  mixed   $default
 * @return string|array
 */
function cms_collections($key = null, $default = [])
{
    if (! is_null($key)) {
        return config('cms.collections.' . $key, $default);
    }

    return config('cms.collections', $default);
}

/**
 * Get the inner collection config.
 *
 * @param  string  $key
 * @param  mixed   $default
 * @return array
 */
function inner_collection($key = null, $default = [])
{
    if (! is_null($key)) {
        return config('cms.inner_collections.' . $key, $default);
    }

    return config('cms.inner_collections', $default);
}

/**
 * Get the files config.
 *
 * @param  string  $key
 * @param  mixed   $default
 * @return string|array
 */
function cms_files($key = null, $default = [])
{
    if (! is_null($key)) {
        return config('cms.files.' . $key, $default);
    }

    return config('cms.files', $default);
}

/**
 * Get the icon name.
 *
 * @param  string  $key
 * @param  mixed   $default
 * @return string|array
 */
function icon_type($key, $default = null)
{
    return config('cms.icons.' . $key, $default);
}

/**
 * Get the path for the glide server.
 *
 * @param  string  $path
 * @param  string  $type
 * @param  string|null  $crop
 * @return string
 */
function glide($path, $type, $crop = null)
{
    $config = config();

    $files = '/' . current((array) $config['elfinder.dir']) . '/';

    if (($pos = strpos($path, $files)) !== false) {
        $glideBaseUrl = '/' . $config['site.glide_base_url'] . '/';

        $query = '?type=' . $type;

        if (! is_null($crop)) {
            $query .= '&crop=' . $crop;
        }

        return substr_replace($path, $glideBaseUrl, $pos, strlen($files)) . $query;
    }

    return $path;
}

/**
 * Convert bytes to human readable format.
 *
 * @param  int  $bytes
 * @param  int  $precision
 * @return string
 */
function format_bytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = (float) max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Cut the text after the limit and breakpoint.
 *
 * @param  string  $string
 * @param  int     $limit
 * @param  string  $break
 * @param  string  $end
 * @return string
 */
function text_limit($string, $limit = 100, $break = '.', $end = '')
{
    $string = str_replace('&nbsp;', ' ', strip_tags($string));
    $string = preg_replace('/\s\s+/', ' ', $string);

    if (mb_strlen($string, 'utf-8') <= $limit) {
        return $string;
    }

    $breakpoint = $break ? mb_strpos($string, $break, $limit, 'utf-8') : $limit;

    if ($breakpoint !== false && $breakpoint < mb_strlen($string, 'utf-8') - 1) {
        $string = mb_substr($string, 0, $breakpoint, 'utf-8') . $end;
    }

    return $string;
}

/**
 * Get youtube video id from url.
 *
 * @param  string  $url
 * @return string
 */
function getYoutubeId($url)
{
    $parts = parse_url($url);

    if (isset($parts['query'])) {
        parse_str($parts['query'], $queryString);

        if (isset($queryString['v'])) {
            return $queryString['v'];
        } elseif (isset($queryString['vi'])) {
            return $queryString['vi'];
        }
    }

    if (isset($parts['path'])) {
        $path = explode('/', trim($parts['path'], '/'));

        return (string) end($path);
    }

    return;
}

/**
 * Convert youtube video url to embed url.
 *
 * @param  string  $url
 * @return string
 */
function getYoutubeEmbed($url)
{
    return 'https://www.youtube.com/embed/' . getYoutubeId($url);
}

/**
 * Calculate age based on the date.
 *
 * @param  string  $dob
 * @return int
 */
function getAge($dob)
{
    $dob = new DateTime($dob);

    $today = new DateTime('today');

    $age = $dob->diff($today)->y;

    return $age;
}

/**
 * Register a database query listener and log the queries.
 *
 * @return void
 */
function log_executed_db_queries()
{
    $filename = storage_path('logs/queries.log');
    $separator = '--------------------------------------------------------' . PHP_EOL;

    if (file_exists($filename)) {
        @unlink($filename);
    }

    file_put_contents($filename, $separator);

    app('events')->listen(QueryExecuted::class, function($query) use ($filename, $separator) {
        $conn     = 'Connection: ' . $query->connectionName . PHP_EOL;
        $sql      = 'SQL: ' . $query->sql . PHP_EOL;
        $bindings = 'Bindings: ' . implode(', ', (array) $query->bindings) . PHP_EOL;
        $time     = 'Time: ' . $query->time . PHP_EOL;
        $data     = $conn . $sql . $bindings . $time . $separator;

        $flags = FILE_APPEND | LOCK_EX;

        file_put_contents($filename, $data, $flags);
    });
}

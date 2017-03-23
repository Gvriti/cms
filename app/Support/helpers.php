<?php

use Models\Abstracts\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Events\QueryExecuted;

/**
 * Get the application default language.
 *
 * @param  string|null  $key
 * @param  string  $value
 * @return string
 */
function language($key = null, $value = 'full_name')
{
    if (is_null($key)) {
        return (string) config('app.language');
    }

    if (! is_null($value)) {
        $value = '.full_name';
    }

    return config("app.languages.{$key}{$value}");
}

/**
 * Get the application languages.
 *
 * @return array
 */
function languages()
{
    return (array) config('app.languages', []);
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
 * Determine if the application is multilanguage.
 *
 * @return bool
 */
function is_multilanguage()
{
    return count(languages()) > 1;
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
 * @param  bool    $throwException
 * @return string
 *
 * @throws \InvalidArgumentException
 */
function cms_route($name, $parameters = [], $language = null, $absolute = true, $throwException = true)
{
    try {
        $route = route($name . '.' . cms_slug(), $parameters, $absolute);
    } catch (InvalidArgumentException $e) {
        if ($throwException) {
            throw $e;
        } else {
            return '/#not_found';
        }
    }

    return add_language($route, $language, $throwException);
}

/**
 * Generate a CMS URL.
 *
 * @param  string  $path
 * @param  array  $parameters
 * @param  string|null  $language
 * @param  bool  $secure
 * @return string
 */
function cms_url($path = null, array $parameters = [], $language = null, $secure = null)
{
    return url(prefix_language(cms_slug($path), $language), [], $secure) . query_string($parameters);
}

/**
 * Generate a web URL to a named route.
 *
 * @param  string  $name
 * @param  mixed   $parameters
 * @param  string  $language
 * @param  bool    $absolute
 * @param  bool    $throwException
 * @return string
 *
 * @throws \InvalidArgumentException
 */
function web_route($name, $parameters = [], $language = null, $absolute = true, $throwException = true)
{
    try {
        $route = route($name, $parameters, $absolute);
    } catch (InvalidArgumentException $e) {
        if ($throwException) {
            throw $e;
        } else {
            return '/#not_found';
        }
    }

    return add_language($route, $language, $throwException);
}

/**
 * Generate a web URL.
 *
 * @param  string  $path
 * @param  array   $parameters
 * @param  string  $language
 * @param  bool    $secure
 * @return string
 */
function web_url($path = null, array $parameters = [], $language = null, $secure = null)
{
    return url(prefix_language($path, $language), [], $secure) . query_string($parameters);
}

/**
 * Build a query string from an array of key value pairs.
 *
 * @param  array  $parameters
 * @param  string  $basePrefix
 * @return string
 */
function query_string(array $parameters, $basePrefix = '?')
{
    if (count($parameters) == 0) {
        return '';
    }

    $query = http_build_query(
        $keyed = array_filter($parameters, 'is_string', ARRAY_FILTER_USE_KEY)
    );

    if (count($keyed) < count($parameters)) {
        $query .= '&'.implode(
                '&', array_filter($parameters, 'is_numeric', ARRAY_FILTER_USE_KEY)
            );
    }

    return $basePrefix.trim($query, '&');
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
    } elseif ($language !== false
        && ($language === true || language_isset())
        && count(languages()) > 1
    ) {
        $path = language() . '/' . $path;
    }

    return $path;
}

/**
 * Add language to the url.
 *
 * @param  string  $url
 * @param  string  $language
 * @param  bool    $throwException
 * @return string
 */
function add_language($url, $language, $throwException = true)
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

        $query = isset($segments['query']) ? '?' . $segments['query'] : '';

        return $request->root() . '/' . implode('/', $path) . $query;
    } elseif (! is_null($language) && $throwException) {
        throw new InvalidArgumentException("Language [{$language}] not defined.");
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
    return config('web.home.' . language());
}

/**
 * Make a nestable eloquent models tree.
 *
 * @param  \Illuminate\Support\Collection|array  $items
 * @param  string|null  $slug
 * @param  int  $parentId
 * @param  string  $parentKey
 * @param  string  $key
 * @return \Illuminate\Support\Collection|array
 */
function make_model_tree($items, $slug = null, $parentId = 0, $parentKey = 'parent_id', $key = 'id')
{
    if (! $items instanceof Collection && ! is_array($items)) {
        throw new InvalidArgumentException(
            'Argument 1 must be of the type array or an instance of ' . Collection::class
        );
    }

    $tree = [];

    $prevSlug = $slug;

    foreach($items as $item) {
        if (! $item instanceof Model) {
            return $items;
        }

        if ($item->{$parentKey} != $parentId) {
            continue;
        }

        if (! is_null($slug)) {
            $slug = $prevSlug ? $prevSlug . '/' . $item->slug : $item->slug;

            $item->original_slug = $item->slug;

            $item->slug = $slug;
        }

        $item->subItems = make_model_tree($items, $slug, $item->{$key}, $parentKey, $key);

        $tree[] = $item;
    }

    return new Collection($tree);
}

/**
 * Determine if the item has a nestable eloquent model items.
 *
 * @param  mixed $item
 * @return bool
 */
function has_model_tree($item)
{
    return $item instanceof Model
        && $item->subItems instanceof Collection
        && $item->subItems->isNotEmpty();
}

/**
 * Get the instance from the container.
 *
 * @param  string  $instance
 * @param  mixed   $default
 * @return mixed
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

    $files = (array) $config['elfinder.dir'];
    $files = current($files) . '/';

    if (($pos = strpos($path, $files)) !== false) {
        $baseUrl = '/' . $config['web.glide_base_url'] . '/';

        $query = '?type=' . $type;

        if (! is_null($crop)) {
            $query .= '&crop=' . $crop;
        }

        return substr_replace($path, $baseUrl, $pos, strlen($files)) . $query;
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
 * @param  array  $allowQueryStrings
 * @param  bool  $strict
 * @return string
 */
function get_youtube_id($url, array $allowQueryStrings = [], $strict = false)
{
    $parts = parse_url($url);

    if (isset($parts['query'])) {
        parse_str($parts['query'], $queryString);

        $allowQueryStrings = query_string(array_intersect_key(
            $queryString, array_flip($allowQueryStrings)
        ), '&');

        if (isset($queryString['v'])) {
            return $queryString['v'] . $allowQueryStrings;
        } elseif (isset($queryString['vi'])) {
            return $queryString['vi'] . $allowQueryStrings;
        }
    } else {
        $allowQueryStrings = '';
    }

    if ((! $strict || isset($parts['scheme'])) && isset($parts['path'])) {
        $path = explode('/', trim($parts['path'], '/'));

        return (string) end($path) . $allowQueryStrings;
    }

    return '';
}

/**
 * Convert youtube video url to embed url.
 *
 * @param  string  $url
 * @param  array  $allowQueryStrings
 * @return string
 */
function get_youtube_embed($url, array $allowQueryStrings = [])
{
    return 'https://www.youtube.com/embed/' . get_youtube_id($url, $allowQueryStrings);
}

/**
 * Calculate age based on the date.
 *
 * @param  string  $dob
 * @return int
 */
function get_age($dob)
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

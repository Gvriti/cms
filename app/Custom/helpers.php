<?php

/**
 * Get the application default language.
 *
 * @param  string|null  $value
 * @return string
 */
function language($value = null)
{
    if (is_null($value)) {
        return config('app.language');
    }

    return config('app.languages.' . $value);
}

/**
 * Get all application languages.
 *
 * @return array
 */
function languages()
{
    return config('app.languages', []);
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
 * Get the available multi-auth instance.
 *
 * @param  string|null  $method
 * @return \Custom\Auth\Auth
 */
function multi_auth($method = null)
{
    if (is_null($method)) {
        return app('Custom\Auth\Auth');
    }

    return app('Custom\Auth\Auth')->$method();
}

/**
 * Determine if the CMS routes should be loaded.
 *
 * @return bool
 */
function cms_will_load()
{
    return config('cms_will_load', false);
}

/**
 * Get the Eloquent model name.
 *
 * @param  string  $name
 * @return string
 */
function get_model_name($name)
{
    return 'Models\\' . ucfirst(str_singular($name));
}

/**
 * Get a cms slug
 *
 * @param  bool|string  $language
 * @return string
 */
function cms_slug($language = false)
{
    $slug = config('cms.slug');

    if ($language === false) return $slug;

    if (is_string($language)) {
        $slug = $language . '/' . $slug;
    } elseif (($language === true || language_isset()) && count(languages()) > 1) {
        $slug = language() . '/' . $slug;
    }

    return $slug;
}

/**
 * Add a cms name to the name of the route.
 *
 * @param  string  $name
 * @param  bool    $resource
 * @return string|array
 */
function cms_prefix($name, $resource = false)
{
    $prefixedName = cms_slug() . '.' . $name;

    if (! $resource) return $prefixedName;

    return [
        'index'   => $prefixedName . '.index',
        'create'  => $prefixedName . '.create',
        'store'   => $prefixedName . '.store',
        'show'    => $prefixedName . '.show',
        'edit'    => $prefixedName . '.edit',
        'update'  => $prefixedName . '.update',
        'destroy' => $prefixedName . '.destroy'
    ];
}

/**
 * Generate a CMS URL to a named route.
 *
 * @param  string  $name
 * @param  array   $parameters
 * @param  string  $language
 * @param  bool    $absolute
 * @param  \Illuminate\Routing\Route  $route
 * @return string
 */
function cms_route($name, $parameters = [], $language = null, $absolute = true, $route = null)
{
    try {
        $route = route(cms_slug() . '.' . $name, $parameters, $absolute, $route);
    } catch (Exception $e) {
        return '#not-found';
    }

    $route = parse_route($route, $language);

    return $route;
}

/**
 * Generate a CMS URL.
 *
 * @param  string  $path
 * @param  mixed   $parameters
 * @param  bool    $secure
 * @return string
 */
function cms_url($path = null, $parameters = [], $language = null, $secure = null)
{
    if (is_array($path)) {
        $path = implode('/', array_filter($path));
    }

    $query = $parameters ? '?' . http_build_query((array) $parameters) : '';

    $path = trim($path, '/');

    return url(cms_slug($language) . '/' . $path, [], $secure) . $query;
}

/**
 * Generate a URL to a named route.
 *
 * @param  string  $name
 * @param  array   $parameters
 * @param  string  $language
 * @param  bool    $absolute
 * @param  \Illuminate\Routing\Route  $route
 * @return string
 */
function site_route($name, $parameters = [], $language = null, $absolute = true, $route = null)
{
    try {
        $route = route($name, $parameters, $absolute, $route);
    } catch (Exception $e) {
        return '#not-found';
    }

    $route = parse_route($route, $language);

    return $route;
}

/**
 * Generate a Site URL.
 *
 * @param  string  $path
 * @param  mixed   $parameters
 * @param  string  $language
 * @param  bool    $secure
 * @return string
 */
function site_url($path = null, $parameters = [], $language = null, $secure = null)
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

    $query = $parameters ? '?' . http_build_query((array) $parameters) : '';

    return url($path, [], $secure) . $query;
}

/**
 * Parse URL generated by route.
 *
 * @param  string  $route
 * @param  string  $language
 * @return string
 */
function parse_route($route, $language)
{
    $languageList = languages();

    if (! is_null($language) && array_key_exists($language, $languageList)) {
        $segments = parse_url($route);

        if (! isset($segments['path'])) return $route;

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

    return $route;
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
 * @param  bool    $render
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
function user_roles($key = null, $default = [])
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
 * @return string|array
 */
function icon_type($key)
{
    return config('cms.icons.' . $key);
}

/**
 * Get the path for the glide server.
 *
 * @param  string  $path
 * @param  string  $type
 * @param  string  $crop
 * @return string
 */
function glide($path, $type, $crop = '')
{
    $config = config();

    $files = '/' . current((array) $config['elfinder.dir']) . '/';

    $pos = strpos($path, $files);

    if ($pos !== false) {
        $glideBaseUrl = '/' . $config['site.glide_base_url'] . '/';

        $query = '?type=' . $type;

        if ($crop) {
            $query .= '&crop=' . $crop;
        }

        return substr_replace($path, $glideBaseUrl, $pos, strlen($files)) . $query;
    }

    return $path;
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
 * @param  string $dob
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
function log_executed_sql_queries()
{
    $filename = storage_path('logs/queries.log');
    $separator = '--------------------------------------------------------' . PHP_EOL;

    if (file_exists($filename)) {
        unlink($filename);
    }

    file_put_contents($filename, $separator);

    app('events')->listen('illuminate.query', function($sql, $bindings, $time) use ($filename, $separator) {
        $sql      = 'SQL: ' . $sql . PHP_EOL;
        $bindings = $bindings ? 'Bindings: ' . implode(', ', $bindings) . PHP_EOL : '';
        $time     = 'Time: ' . $time . PHP_EOL;
        $data     = $sql . $bindings . $time . $separator;

        $flags = FILE_APPEND | LOCK_EX;

        file_put_contents($filename, $data, $flags);
    });
}

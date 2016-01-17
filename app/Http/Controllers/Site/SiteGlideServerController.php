<?php

namespace App\Http\Controllers\Site;

use League\Glide\Server;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Glide\Http\NotFoundException;
use Illuminate\Config\Repository as Config;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteGlideServerController extends Controller
{
    /**
     * The Server instance.
     *
     * @var \League\Glide\Server
     */
    protected $server;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Server $server, Request $request)
    {
        $this->server = $server;

        $this->request = $request;
    }

    /**
     * Display the specified image.
     *
     * @param  \Illuminate\Config\Repository  $config
     * @param  string  $path
     * @return Request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function show(Config $config, $path)
    {
        $settings = $config['site.glide.' . $this->request->get('type')];

        if ($crop = $this->request->get('crop')) {
            $settings['crop'] = $crop;
        }

        if (! is_array($settings)) {
            $filesDir = current((array) $config['elfinder.dir']);

            return redirect($filesDir . '/' . $path);
        }

        try {
            return $this->server->outputImage($path, $settings);
        } catch (NotFoundException $e) {
            return $path;
        }
    }
}

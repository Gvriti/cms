<?php

namespace App\Http\Controllers\Admin;

use DOMDocument;
use Models\Page;
use Models\Abstracts\Model;
use Illuminate\Http\Request;
use Sabre\Xml\Service as XmlService;
use App\Http\Controllers\Controller;

class AdminSitemapXmlController extends Controller
{
    /**
     * XML data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * List of default namespace prefixes.
     *
     * @var array
     */
    protected $namespaceMap = ['http://www.sitemaps.org/schemas/sitemap/0.9' => ''];

    /**
     * xhtml namespace prefix.
     *
     * @var string
     */
    protected $xhtml;

    /**
     * List of the application languages.
     *
     * @var array
     */
    protected $languages = [];

    /**
     * Main language of the application.
     *
     * @var string
     */
    protected $mainLanguage;

    /**
     * Indicates if the application has many languages.
     *
     * @var bool
     */
    protected $hasManyLang = false;

    /**
     * List of the attached types.
     *
     * @var array
     */
    protected $attachedTypes = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->languages = languages();

        $this->mainLanguage = key($this->languages);

        if ($this->hasManyLang = (count($this->languages) > 1)) {
            $this->namespaceMap += [
                $this->xhtml = 'http://www.w3.org/1999/xhtml' => 'xhtml'
            ];
        }

        $this->attachedTypes = cms_pages('attached');
    }

    /**
     * Store a newly created/update sitemap xml.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $pages = (new Page)->visible()->orderBy('menu_id')->positionAsc()->get();

        foreach ($pages as $item) {
            $value = ['url' => ['loc' => web_url(
                $item->fullSlug = $item->fullSlug()->slug, [], $this->hasManyLang
                ? $this->mainLanguage
                : null
            )]];

            if ($item->hasLanguages() && $this->hasManyLang) {
                foreach ($this->languages as $langKey => $langValue) {
                    $value['url'][] = $this->getLanguageLinks($item, null, $langKey);
                }
            }

            $this->data[] = $value;

            $this->setImplicitModels($item, $this->attachedTypes);
        }

        $xml = new XmlService;
        $xml->namespaceMap = $this->namespaceMap;

        $doc = new DOMDocument;
        $doc->loadXML($xml->write("urlset", $this->data));

        $result = $doc->save(public_path('sitemap.xml'));

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()->back();
    }

    /**
     * Set an implicit models to the xml data.
     *
     * @param  \Models\Abstracts\Model $item
     * @return void
     */
    protected function setImplicitModels(Model $item)
    {
        if (! in_array($item->type, $this->attachedTypes)) {
            return;
        }

        $implicitModel = model_path($item->type);

        $implicitModel = (new $implicitModel)->find($item->type_id);

        if (! is_null($implicitModel)) {
            $model = model_path($implicitModel->type);

            $items = (new $model)->where(
                    str_singular($implicitModel->getTable()) . '_id',
                    $implicitModel->id
                )->visible()->orderDesc()->get();

            foreach ($items as $implicitItem) {
                if (empty($implicitItem->slug)) {
                    continue;
                }

                $value = ['url' => ['loc' => web_url(
                    [$item->fullSlug, $implicitItem->slug], [], $this->hasManyLang
                    ? $this->mainLanguage
                    : null
                )]];

                if ($implicitItem->hasLanguages() && $this->hasManyLang) {
                    foreach ($this->languages as $langKey => $langValue) {
                        $value['url'][] = $this->getLanguageLinks(
                            $item, $implicitItem->slug, $langKey
                        );
                    }
                }

                $this->data[] = $value;
            }
        }
    }

    /**
     * Get an array of xml language links.
     *
     * @param  \Models\Abstracts\Model $item
     * @param  string|null $slug
     * @param  string $langKey
     * @return array
     */
    protected function getLanguageLinks(Model $item, $slug = null, $langKey)
    {
        return [
            'name' => "{{$this->xhtml}}link",
            'attributes' => [
                'rel' => 'alternate',
                'hreflang' => $langKey,
                'href' => web_url(
                    [$item->fullSlug, $slug],
                    [],
                    $langKey
                )
            ]
        ];
    }
}

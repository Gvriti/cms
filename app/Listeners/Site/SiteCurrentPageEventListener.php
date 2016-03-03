<?php

namespace App\Listeners\Site;

class SiteCurrentPageEventListener
{
    /**
     * Handle view composer data.
     *
     * @param  \Illuminate\Contracts\View\View  $event
     * @return void
     */
    public function onCurrentPageComposer($event)
    {
        $current = $event->current;

        $trans = app_instance('trans')->lists('value', 'name');

        if (! is_object($current)) {
            $current = (object) [
                'id'        => 0,
                'title'     => $title = $trans->get('site_title'),
                'slug'      => $this->getPath(),
                'image'     => asset('assets/site/images/logo.png'),
                'meta_desc' => $trans->get('meta_desc') ?: $title,
            ];
        } else {
            if (empty($current->id)) {
                $current->id = 0;
            }

            if (! empty($current->tab_title)) {
                $current->title .= ' - ' . $current->tab_title;
            }

            if (empty($current->slug)) {
                $current->slug = $this->getPath();

                $current->original_slug = basename($current->slug);
            } elseif (! empty($current->tab_slug)) {
                $current->slug .= '/' . $current->tab_slug;
            }

            if (empty($current->meta_desc)) {
                if (! empty($current->description)) {
                    $current->meta_desc = text_limit($current->description);
                } elseif (! empty($current->content)) {
                    $current->meta_desc = text_limit($current->content);
                } else {
                    $current->meta_desc = text_limit($current->title);
                }
            }

            if (empty($current->image)) {
                $current->image = asset('assets/site/images/logo.png');
            }
        }

        $event->current = $current;
    }

    /**
     * Get the current path without language prefix.
     *
     * @return string
     */
    protected function getPath()
    {
        $path = trim(request()->getPathInfo(), '/');

        if (strpos($path, $language = language()) === 0) {
            $path = substr($path, strlen($language));
        }

        return $path;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen([
                'composing: site.partials.head',
                'composing: site.partials.pages',
            ],
            'App\Listeners\Site\SiteCurrentPageEventListener@onCurrentPageComposer'
        );
    }
}

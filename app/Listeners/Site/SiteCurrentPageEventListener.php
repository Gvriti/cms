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

        $trans = app_instance('trans');

        if (! is_object($current)) {
            $current = (object) [
                'id'        => 0,
                'title'     => $title = $trans->get('site_title'),
                'slug'      => '',
                'image'     => asset('assets/site/images/logo.png'),
                'meta_desc' => $trans->get('meta_desc') ?: $title,
            ];
        } elseif (empty($current->meta_desc)) {
            if (! empty($current->description)) {
                $current->meta_desc = text_limit($current->description);
            } else {
                $current->meta_desc = text_limit($current->title);
            }

            if (empty($current->image)) {
                $current->image = asset('assets/site/images/logo.png');
            }
        }

        $event->current = $current;
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
                'composing: site.home',
            ],
            'App\Listeners\Site\SiteCurrentPageEventListener@onCurrentPageComposer'
        );
    }
}

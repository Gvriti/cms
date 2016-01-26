<?php

namespace App\Listeners\Site;

class SiteBreadcrumbEventListener
{
    /**
     * Handle view composer data.
     *
     * @param  \Illuminate\Contracts\View\View  $event
     * @return void
     */
    public function onBreadcrumbComposer($event)
    {
        if (is_object($event->current)) {
            $breadcrumb = app_instance('breadcrumb');

            if (($parent = $breadcrumb->last()) !== $event->current) {
                $event->current->original_slug = $event->current->slug;

                $event->current->parent_slug = $parent->slug;

                $event->current->slug = $parent->slug . '/' . $event->current->slug;

                $breadcrumb->push($event->current);

                if (! is_null($event->current->tab_slug)
                    && ! is_null($event->current->tab_title)
                ) {
                    $current = $event->current->newInstance();

                    $current->slug = $event->current->tab_slug;

                    $current->title = $event->current->tab_title;

                    $breadcrumb->push($current);
                }
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen(
            'composing: site.partials.breadcrumb',
            'App\Listeners\Site\SiteBreadcrumbEventListener@onBreadcrumbComposer'
        );
    }
}

<?php

namespace App\Listeners\Site;

class SiteViewEventListener
{
    /**
     * Handle view composer events.
     *
     * @param  \Illuminate\Contracts\View\View  $event
     * @return void
     */
    public function onViewComposer($event)
    {
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            'composing: site.app',
            'App\Listeners\Site\SiteViewEventListener@onViewComposer'
        );
    }
}
<?php

namespace App\Listeners\Site;

class SiteViewEventListener
{
    /**
     * Handle view composer data.
     *
     * @param  \Illuminate\Contracts\View\View  $event
     * @return void
     */
    public function onViewComposer($event)
    {
        $data = $this->getData();

        if (is_array($data)) {
            foreach ($data as $key => $item) {
                $event->offsetSet($key, $item);
            }
        }
    }

    /**
     * Get all data.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        return $data;
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
            'composing: site.app',
            'App\Listeners\Site\SiteViewEventListener@onViewComposer'
        );
    }
}
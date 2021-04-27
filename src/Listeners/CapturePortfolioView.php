<?php

namespace Canvas\Listeners;

use Canvas\Canvas;
use Canvas\Events\PortfolioViewed;
use Canvas\Models\Portfolio;

class CapturePortfolioView
{
    /**
     * A view is captured when a user loads a post for the first time in a given
     * hour. The ID of the post is stored in session to be validated against
     * until it "expires" and is pruned by the Session middleware class.
     *
     * @param PortfolioViewed $event
     * @return void
     */
    public function handle(PortfolioViewed $event)
    {
        if (! $this->wasRecentlyViewed($event->portfolio)) {
            $referer = request()->header('referer');

            $data = [
                'portfolio_id' => $event->portfolio->id,
                'ip' => request()->getClientIp(),
                'agent' => request()->header('user_agent'),
                'referer' => Canvas::isValidUrl($referer) ? Canvas::trimUrl($referer) : false,
            ];

            $event->portfolio->views()->create($data);

            $this->storeInSession($event->portfolio);
        }
    }

    /**
     * Check if a given post exists in the session.
     *
     * @param Portfolio $portfolio
     * @return bool
     */
    protected function wasRecentlyViewed(Portfolio $portfolio): bool
    {
        $viewed = session()->get('viewed_portfolios', []);

        return array_key_exists($portfolio->id, $viewed);
    }

    /**
     * Add a given post to the session.
     *
     * @param Portfolio $portfolio
     * @return void
     */
    protected function storeInSession(Portfolio $portfolio)
    {
        session()->put("viewed_portfolios.{$portfolio->id}", now()->timestamp);
    }
}

<?php

namespace Canvas\Listeners;

use Canvas\Canvas;
use Canvas\Events\PortfolioViewed;
use Canvas\Models\Portfolio;

class CapturePortfolioVisit
{
    /**
     * A visit is captured when a user loads a post for the first time in a given
     * day. The post ID and the IP of the request are both stored in session to
     * be validated against until pruned by the Session middleware class.
     *
     * @param PortfolioViewed $event
     * @return void
     */
    public function handle(PortfolioViewed $event)
    {
        $ip = request()->getClientIp();

        if ($this->visitIsUnique($event->portfolio, $ip)) {
            $referer = request()->header('referer');

            $data = [
                'portfolio_id' => $event->portfolio->id,
                'ip' => $ip,
                'agent' => request()->header('user_agent'),
                'referer' => Canvas::isValidUrl($referer) ? Canvas::trimUrl($referer) : false,
            ];

            $event->portfolio->visits()->create($data);

            $this->storeInSession($event->portfolio, $ip);
        }
    }

    /**
     * Check if a given post and IP are unique to the session.
     *
     * @param Portfolio $portfolio
     * @param string $ip
     * @return bool
     */
    protected function visitIsUnique(Portfolio $portfolio, string $ip): bool
    {
        $visits = session()->get('visited_portfolios', []);

        if (array_key_exists($portfolio->id, $visits)) {
            $visit = $visits[$portfolio->id];

            return $visit['ip'] != $ip;
        } else {
            return true;
        }
    }

    /**
     * Add a given post and IP to the session.
     *
     * @param Portfolio $portfolio
     * @param string $ip
     * @return void
     */
    protected function storeInSession(Portfolio $portfolio, string $ip)
    {
        session()->put("visited_portfolios.{$portfolio->id}", [
            'timestamp' => now()->timestamp,
            'ip' => $ip,
        ]);
    }
}

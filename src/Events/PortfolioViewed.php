<?php

namespace Canvas\Events;

use Canvas\Models\Portfolio;

class PortfolioViewed
{
    /**
     * The portfolio instance.
     *
     * @var Portfolio
     */
    public $portfolio;

    /**
     * Create a new event instance.
     *
     * @param Portfolio $portfolio
     */
    public function __construct(Portfolio $portfolio)
    {
        $this->portfolio = $portfolio;
    }
}

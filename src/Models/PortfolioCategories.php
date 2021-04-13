<?php

namespace Canvas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PortfolioCategories extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'canvas_portfolios_categories';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the portfolios relationship.
     *
     * @return BelongsTo
     */
    public function portfolios(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    /**
     * Get the categories relationship.
     *
     * @return BelongsTo
     */
    public function portfolio_category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

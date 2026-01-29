<?php

namespace Backend\Infrastructure\Auth;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Get the tokenable model that this access token belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function tokenable()
    {
        return $this->morphTo();
    }
}

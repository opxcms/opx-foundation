<?php

namespace Core\Traits\Site;

trait Profile
{
    /**
     * Get current profile key.
     *
     * @return  string
     */
    public function profile(): string
    {
        if ($this->profile) {
            return $this->profile;
        }

        return $this->app['opx.profile'] ?? 'default';
    }
}
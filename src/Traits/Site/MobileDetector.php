<?php

namespace Core\Traits\Site;

use Detection\MobileDetect;

trait MobileDetector
{
    /**
     * Mobile detector.
     *
     * @return  \Detection\MobileDetect
     */
    public function mobileDetect()
    {
        if(! $this->mobileDetector) {
            $this->mobileDetector = new MobileDetect();
        }

        return $this->mobileDetector;
    }
}
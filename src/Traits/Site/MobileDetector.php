<?php

namespace Core\Traits\Site;

use Detection\MobileDetect;

trait MobileDetector
{
    /**
     * Mobile detector.
     *
     * @return  MobileDetect
     */
    public function mobileDetect(): MobileDetect
    {
        if(! $this->mobileDetector) {
            $this->mobileDetector = new MobileDetect();
        }

        return $this->mobileDetector;
    }
}
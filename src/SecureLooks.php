<?php

namespace ThemeLooks\SecureLooks;

use ThemeLooks\SecureLooks\Trait\Helper;
use ThemeLooks\SecureLooks\Trait\SecureLooksTrait;
use ThemeLooks\SecureLooks\Trait\Config as AppConfiguration;

class SecureLooks
{
    use SecureLooksTrait, Helper, AppConfiguration;
    //Loading app
    public function init()
    {
        if ($this->checkSystem() && !$this->checkSass() && $this->checkTableExists()) {
            $identifiers = $this->getKeys();
            foreach ($identifiers as $identifier) {
                if (!cache()->has('license-valid-' . $identifier->license_key)) {
                    $this->registerDomain($identifier->license_key, $identifier->item);
                }

                if (cache()->has('license-valid-' . $identifier->license_key) && !cache()->get('license-valid-' . $identifier->license_key)) {
                    $this->registerDomain($identifier->license_key, $identifier->item);
                }
            }
        }
    }
    //Create app instance
    public function createApp($purchase_key, $redirect = true, $api_url = null, $item = null)
    {
        return $this->createAppInstance($purchase_key, $api_url, $redirect, $item);
    }

    //Validate app instance
    public function validateApp($purchase_key)
    {
        return $this->registerDomain($purchase_key, config('themelooks.item'), true);
    }

    //Loading app configuration
    public function initApp()
    {
        $this->loadConfig();
    }
}

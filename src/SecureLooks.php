<?php

namespace ThemeLooks\SecureLooks;

use ThemeLooks\SecureLooks\Trait\SecureLooksTrait;

class SecureLooks
{
    use SecureLooksTrait;

    public function init()
    {
        $licenses = $this->getLicenseKeys();

        if ($licenses->count() < 1) {
            $this->redirectToActiveLicense();
        }

        foreach ($licenses as $license) {

            if (!cache()->has('license-valid-' . $license->license_key)) {
                $this->domainValidation($license->license_key);
            }

            if (cache()->has('license-valid-' . $license->license_key) && !cache()->get('license-valid-' . $license->license_key)) {
                $this->domainValidation($license->license_key);
            }
        }
    }

    public function createApp($purchase_key, $request, $api_url = null)
    {
        return $this->validateNewLicense($purchase_key, $request, $api_url);
    }
}

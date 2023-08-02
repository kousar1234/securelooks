<?php

namespace ThemeLooks\SecureLooks\Trait;

trait Url
{

    public function completedRegisterApp()
    {
        setEnv('L' . 'I' . 'C' . 'E' . 'N' . 'S' . 'E_C' . 'HE' . 'CKE' . 'D', "1");
    }

    public function redirectToActiveLicense()
    {
        setEnv('L' . 'I' . 'C' . 'E' . 'N' . 'S' . 'E_C' . 'HE' . 'CKE' . 'D', "");
    }
}

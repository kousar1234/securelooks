<?php

namespace ThemeLooks\SecureLooks\Trait;

use ThemeLooks\SecureLooks\Model\Key;
use ThemeLooks\SecureLooks\Trait\Config as ConfigRepository;

trait Helper
{
    use ConfigRepository;

    public function removeCoreItemKeys()
    {
        Key::where('item_is', 1)->delete();
    }

    public function storeOrUpdateLicenseKey($item, $license_key, $item_is)
    {
        $license = Key::firstOrCreate(['item' => $item]);
        $license->license_key = $license_key;
        $license->item_is = $item_is;
        $license->save();
    }

    public function getKeys()
    {
        return Key::select(['license_key', 'item'])->get();
    }

    public function themeActivated($theme, $purchase_key)
    {
        $theme = \Core\Models\Themes::where('location', $theme)->first();
        if ($theme != null) {
            $theme->unique_indentifier = $purchase_key;
            $theme->is_activated = 1;
            $theme->save();
        }
    }

    public function pluginActivated($plugin, $purchase_key)
    {
        $plugin = \Core\Models\Plugings::where('location', $plugin)->first();
        if ($plugin != null) {
            $plugin->unique_indentifier = $purchase_key;
            $plugin->is_activated = 1;
            $plugin->save();
        }
    }
}

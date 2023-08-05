<?php

namespace ThemeLooks\SecureLooks\Trait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use ThemeLooks\SecureLooks\Model\Key;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use ThemeLooks\SecureLooks\Trait\Config as ConfigRepository;

trait Helper
{
    use ConfigRepository;

    public function getKeys()
    {
        $keys = Cache::rememberForever('user_keys', function () {
            return Key::select(['license_key', 'item'])->get();
        });
        return $keys;
    }

    public function getKeyInfo($key)
    {
        return Key::where('license_key', $key)->first();
    }

    public function storeOrUpdateLicenseKey($item, $license_key, $item_is)
    {
        $license = Key::firstOrCreate(['item' => $item]);
        $license->license_key = $license_key;
        $license->item_is = $item_is;
        $license->save();
        Cache::forget('user_keys');
    }

    public function removeCoreItemKeys()
    {
        Key::where('item_is', 1)->delete();
        Cache::forget('user_keys');
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

    public function checkTableExists()
    {
        try {
            if (!Schema::hasTable('user_keys')) {
                Schema::create('user_keys', function (Blueprint $table) {
                    $table->id();
                    $table->text('license_key')->nullable();
                    $table->string('item')->nullable();
                    $table->integer('item_is')->nullable();
                    $table->timestamps();
                });
            }
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
}

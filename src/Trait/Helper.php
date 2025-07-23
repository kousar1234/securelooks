<?php

namespace ThemeLooks\SecureLooks\Trait;

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

    public function storeOrUpdateLicenseKey($item, $license_key, $item_is, $type)
    {
        if (config('themelooks.type') == 'sass') {
            $license = Key::firstOrCreate(['item' => $item]);
            $license->license_key = $license_key;
            $license->item_is = $item_is;
            $license->type = $type;
            $license->save();
        } else {
            $license = Key::firstOrCreate(['item' => $item]);
            $license->license_key = $license_key;
            $license->item_is = $item_is;
            $license->save();
        }

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

    public function themeDeactivated($theme)
    {
        $theme = \Core\Models\Themes::where('location', $theme)->first();
        if ($theme != null) {
            $theme->is_activated = 2;
            $theme->save();
        }
    }

    public function pluginActivated($plugin, $purchase_key)
    {
        $plugin = \Core\Models\Plugin::where('location', $plugin)->first();
        if ($plugin != null) {
            $plugin->unique_indentifier = $purchase_key;
            $plugin->is_activated = 1;
            $plugin->save();

            $plugin_info = file_get_contents(base_path("plugins/{$plugin->location}/plugin.json"));
            $data = json_decode($plugin_info, true);
            $data['is_verified'] = true;
            file_put_contents(base_path("plugins/{$plugin->location}/plugin.json"), json_encode($data));
        }
    }

    public function pluginDeactivated($plugin)
    {
        $plugin = \Core\Models\Plugin::where('location', $plugin)->first();
        if ($plugin != null) {
            $plugin->is_activated = 2;
            $plugin->save();

            $plugin_info = file_get_contents(base_path("plugins/{$plugin->location}/plugin.json"));
            $data = json_decode($plugin_info, true);
            $data['is_verified'] = false;
            file_put_contents(base_path("plugins/{$plugin->location}/plugin.json"), json_encode($data));
        }
    }

    public function checkTableExists()
    {
        try {
            if (!Schema::hasTable('user_keys')) {
                if (config('themelooks.type') == 'sass') {
                    Schema::create('user_keys', function (Blueprint $table) {
                        $table->id();
                        $table->text('license_key')->nullable();
                        $table->string('item')->nullable();
                        $table->integer('item_is')->nullable();
                        $table->string('type')->nullable();
                        $table->timestamps();
                    });
                } else {
                    Schema::create('user_keys', function (Blueprint $table) {
                        $table->id();
                        $table->text('license_key')->nullable();
                        $table->string('item')->nullable();
                        $table->integer('item_is')->nullable();
                        $table->timestamps();
                    });
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
}

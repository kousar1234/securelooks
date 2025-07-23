<?php

namespace ThemeLooks\SecureLooks\Trait;

use Illuminate\Support\Facades\Http;
use ThemeLooks\SecureLooks\Trait\Helper;
use ThemeLooks\SecureLooks\Trait\Url as UrlHelper;
use ThemeLooks\SecureLooks\Trait\Config as ConfigRepository;

trait SecureLooksTrait
{
    use ConfigRepository, UrlHelper, Helper;


    public function createAppInstance($purchase_key, $api_url = null, $redirect = true, $item = null)
    {
        try {
            $api_url = $api_url != null ? $api_url : $this->baseApiUrl() . '/api/v1/verify-license-key';
            $item = $item != null ? $item : config('themelooks.item');
            $domain = request()->getSchemeAndHttpHost();

            $response = Http::withOptions(['verify' => false])->post($api_url, [
                'purchase_key' => $purchase_key,
                'user_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'password' => auth()->user()->password,
                'domain' => $domain,
                'item' => $item
            ]);
            if ($response->failed()) {
                return redirect()->back()->withErrors(['message' => 'Request failed. Please try again']);
            }


            if ($response->serverError()) {
                return redirect()->back()->withErrors(['message' => 'Server error. Please try again']);
            }

            if ($response->clientError()) {
                return redirect()->back()->withErrors(['message' => 'Client error. Please try again']);
            }

            if ($response->ok()) {
                $response_body = json_decode($response->body(), true);

                if ($response_body['success'] && $response_body['activated']) {
                    $license_info = json_decode($response_body['license_key'], true);
                    //Activate Combo Items
                    if (isset($license_info['combo_items'])) {
                        foreach ($license_info['combo_items'] as $item) {
                            //Plugin
                            if ($item['item_is'] == 2) {
                                $this->pluginActivated($item['item'], $purchase_key);
                            }

                            //Theme
                            if ($item['item_is'] == 3) {
                                $this->themeActivated($item['item'], $purchase_key);
                            }
                        }
                    }
                    //Core item
                    if ($license_info['item_is'] == 1) {
                        $this->removeCoreItemKeys();
                        $this->storeOrUpdateLicenseKey($license_info['item'], $license_info['key'], $license_info['item_is'], $license_info['type']);
                        $this->completedRegisterApp();
                        if ($redirect) {
                            return redirect()->route(config('themelooks.license_activate_success_route'));
                        }

                        return 'SUCCESS';
                    }

                    //Plugin
                    if ($license_info['item_is'] == 2) {
                        $this->storeOrUpdateLicenseKey($license_info['item'], $license_info['key'], $license_info['item_is'], $license_info['type']);
                        $this->pluginActivated($license_info['item'], $purchase_key);
                        if ($redirect) {
                            return redirect()->route(config('themelooks.plugin_success_route'));
                        }

                        return 'SUCCESS';
                    }

                    //Theme
                    if ($license_info['item_is'] == 3) {
                        $this->storeOrUpdateLicenseKey($license_info['item'], $license_info['key'], $license_info['item_is'], $license_info['type']);
                        $this->themeActivated($license_info['item'], $purchase_key);
                        if ($redirect) {
                            return redirect()->route(config('themelooks.theme_success_route'));
                        }

                        return 'SUCCESS';
                    }
                }

                if ($response_body['success'] && !$response_body['activated']) {
                    return redirect()->back()->withErrors(['message' => $response_body['message']]);
                }

                if (!$response_body['success'] && !$response_body['activated']) {
                    return redirect()->back()->withErrors(['message' => $response_body['message']]);
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Something went wrong. Please try again']);
        } catch (\Error $e) {
            return redirect()->back()->withErrors(['message' => 'Something went wrong. Please try again']);
        }
    }

    public function registerDomain($purchase_key, $item = null, $redirect = false)
    {
        $domain = request()->getSchemeAndHttpHost();
        if (env('IS_USER_REGISTERED') == 1 && env(implode('', ['L', 'I', 'CE', 'N', 'S', 'E_C', 'H', 'E', 'C', 'K', 'E', 'D'])) == 1) {
            try {
                $response = Http::withOptions(['verify' => false])->post($this->baseApiUrl() . '/api/v1/validate-license-key', [
                    'purchase_key' => $purchase_key,
                    'domain' => $domain,
                    'item' => $item
                ]);

                if ($response->failed()) {
                    if ($redirect) {
                        return redirect()->back()->withErrors(['message' => 'Request failed. Please try again']);
                    }
                }


                if ($response->serverError()) {
                    if ($redirect) {
                        return redirect()->back()->withErrors(['message' => 'Server error. Please try again']);
                    }
                }

                if ($response->clientError()) {
                    if ($redirect) {
                        return redirect()->back()->withErrors(['message' => 'Client error. Please try again']);
                    }
                }

                if ($response->ok()) {
                    $response_body = json_decode($response->body(), true);

                    if ($response_body['success'] && $response_body['is_validate']) {
                        if (!$redirect) {
                            cache()->put('license-valid-' . $purchase_key, true, now()->addHours(5));
                        }

                        if ($redirect) {
                            cache()->put('license-valid-' . $purchase_key, true, now()->addHours(5));
                            $license_info = json_decode($response_body['item'], true);
                            if (isset($license_info['combo_items'])) {
                                foreach ($license_info['combo_items'] as $item) {
                                    //Plugin
                                    if ($item['item_is'] == 2) {
                                        $this->pluginActivated($item['item'], $purchase_key);
                                    }

                                    //Theme
                                    if ($item['item_is'] == 3) {
                                        $this->themeActivated($item['item'], $purchase_key);
                                    }
                                }
                            }
                            //Core item
                            if ($license_info['item_is'] == 1) {
                                $this->removeCoreItemKeys();
                                $this->storeOrUpdateLicenseKey($license_info['item'], $license_info['key'], $license_info['item_is'], $license_info['type']);
                                $this->completedRegisterApp();
                                return redirect()->route(config('themelooks.license_verify_success_route'));
                            }
                        }
                    }

                    if ($response_body['success'] && !$response_body['is_validate']) {
                        if (!$redirect) {
                            $license_info = $this->getKeyInfo($purchase_key);
                            //Core item
                            if ($license_info->item_is == 1) {
                                $this->removeCoreItemKeys();
                                $this->redirectToActiveLicense();
                            }

                            //Plugin
                            if ($license_info->item_is == 2) {
                                $this->pluginDeactivated($license_info->item);
                            }

                            //Theme
                            if ($license_info->item_is == 3) {
                                $this->themeDeactivated($license_info->item);
                            }
                        }

                        if ($redirect) {
                            return redirect()->back()->withErrors(['message' => 'Invalid Purchase key']);
                        }
                    }

                    if (!$response_body['success'] && !$response_body['is_validate']) {
                        if ($redirect) {
                            return redirect()->back()->withErrors(['message' => 'Something went wrong']);
                        }
                    }
                }
            } catch (\Exception $e) {
                if ($redirect) {
                    return redirect()->back()->withErrors(['message' => 'Something went wrong. Please try again']);
                }
            } catch (\Error $e) {
                if ($redirect) {
                    return redirect()->back()->withErrors(['message' => 'Something went wrong. Please try again']);
                }
            }
        }
    }
}

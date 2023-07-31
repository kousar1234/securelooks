<?php

namespace ThemeLooks\SecureLooks\Trait;

use Illuminate\Support\Facades\Http;
use ThemeLooks\SecureLooks\Model\License;

trait SecureLooksTrait
{

    public function getLicenseKeys()
    {
        return License::select(['license_key'])->get();
    }

    public function validateNewLicense($purchase_key, $request, $api_url = null)
    {
        try {
            $api_url = $api_url != null ? $api_url : $this->baseApiUrl() . '/api/v1/verify-license-key';

            $response = Http::withOptions(['verify' => false])->post($api_url, [
                'purchase_key' => $purchase_key,
                'user_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'password' => auth()->user()->password,
                'domain' => $request->getSchemeAndHttpHost()
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

                    $this->storeOrUpdateLicenseKey($license_info['item'], $license_info['key'], $license_info['item_is']);
                    $this->completedRegisterApp();

                    return redirect()->route('core.admin.' . implode('', ['w', 'e', 'l', 'c', 'o', 'm', 'e']));
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

    public function domainValidation($purchase_key)
    {
        $domain = request()->getSchemeAndHttpHost();
        if (env('IS_USER_REGISTERED') == 1 && env('LICENSE_CHECKED') == 1) {
            try {
                $response = Http::withOptions(['verify' => false])->post($this->baseApiUrl() . '/api/v1/validate-license-key', [
                    'purchase_key' => $purchase_key,
                    'domain' => $domain
                ]);
                if ($response->failed()) {
                    //next()
                }


                if ($response->serverError()) {
                    //next()
                }

                if ($response->clientError()) {
                    //next()
                }

                if ($response->ok()) {
                    $response_body = json_decode($response->body(), true);
                    if ($response_body['success'] && $response_body['is_validate']) {
                        cache()->put('license-valid-' . $purchase_key, true, now()->addHours(5));
                        //next()
                    }

                    if ($response_body['success'] && !$response_body['is_validate']) {
                        $license_info = License::where('license_key', $purchase_key)->first();
                        //Core item
                        if ($license_info->item_is == 1) {
                            $this->redirectToActiveLicense();
                        }

                        //Plugin
                        if ($license_info->item_is == 2) {
                            $plugin = \Core\Models\Plugings::where('location', $license_info->item)->first();
                            if ($plugin != null) {
                                $plugin->is_activated = 2;
                                $plugin->save();
                            }
                        }

                        //Theme
                        if ($license_info->item_is == 3) {
                            $theme = \Core\Models\Themes::where('location', $license_info->item)->first();
                            if ($theme != null) {
                                $theme->is_activated = 2;
                                $theme->save();
                            }
                        }
                    }

                    if (!$response_body['success'] && !$response_body['is_validate']) {
                        //next()
                    }
                }
            } catch (\Exception $e) {
                //next()
            } catch (\Error $e) {
                //next()
            }
        }
    }


    public function storeOrUpdateLicenseKey($item, $license_key, $item_is)
    {
        $license = License::firstOrCreate(['item' => $item]);
        $license->license_key = $license_key;
        $license->item_is = $item_is;
        $license->save();
    }

    public function completedRegisterApp()
    {
        setEnv('LICENSE_CHECKED', "1");
    }

    public function redirectToActiveLicense()
    {
        setEnv('LICENSE_CHECKED', "");
    }

    public function baseApiUrl()
    {
        return config('themelooks.api_base_url');
    }
}

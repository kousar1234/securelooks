<?php

namespace ThemeLooks\SecureLooks;

use ThemeLooks\SecureLooks\License;
use Illuminate\Support\Facades\Http;

class SecureLooksService
{

    protected $base_path = "http://tlcommerce.license";

    public function init()
    {
        $licenses = License::select(['license_key'])->get();
        foreach ($licenses as $license) {

            if (!cache()->has('license-valid-' . $license->license_key)) {
                $this->validDomain($license->license_key);
            }

            if (cache()->has('license-valid-' . $license->license_key) && !cache()->get('license-valid-' . $license->license_key)) {
                $this->validDomain($license->license_key);
            }
        }
    }

    public function registerApp($request)
    {
        try {
            $response = Http::withOptions(['verify' => false])->post($this->base_path . '/api/v1/verify-license-key', [
                'purchase_key' => $request['license'],
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

                    $license = License::firstOrCreate(['item' => $license_info['item']]);
                    $license->license_key = $license_info['key'];
                    $license->item_is = $license_info['item_is'];
                    $license->save();

                    setEnv('LICENSE_CHECKED', "1");
                    return redirect()->route('core.admin.welcome');
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

    public function validDomain($purchase_key)
    {
        $domain = request()->getSchemeAndHttpHost();
        if (env('IS_USER_REGISTERED') == 1 && env('LICENSE_CHECKED') == 1) {
            try {
                $response = Http::withOptions(['verify' => false])->post($this->base_path . '/api/v1/validate-license-key', [
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
                            setEnv('LICENSE_CHECKED', "");
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
}

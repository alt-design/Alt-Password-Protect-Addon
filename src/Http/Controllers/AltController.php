<?php namespace AltDesign\AltPasswordProtect\Http\Controllers;

use Illuminate\Http\Request;
use AltDesign\AltPasswordProtect\Helpers\Data;
use Statamic\Auth\Protect\Protectors\Password\Guard;
use Statamic\Auth\Protect\Protectors\Password\Controller as PasswordProtectController;
use AltDesign\AltPasswordProtect\Protectors\CustomGuard;
use Statamic\Facades\Entry;

/**
 * Class AltController
 *
 * @package  AltDesign\AltPasswordProtect
 * @author   Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class AltController extends PasswordProtectController {

    protected $tokenData;
    protected $password;

    /**
     *  Render the default options page.
     */
    public function index()
    {
        $data = new Data('settings');

        $blueprint = $data->getBlueprint(true);
        $fields = $blueprint->fields()->addValues($data->all())->preProcess();

        return view('alt-password-protect::index', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    /**
     * Update the settings.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $data = new Data('settings');

        // Set the fields etc
        $blueprint = $data->getBlueprint(true);
        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        // Save the data
        $data->setAll($fields->process()->values()->toArray());

        return true;
    }

    public function store()
    {
        $this->password = request('password');
        $this->tokenData = session('statamic:protect:password.tokens.'.request('token'));
        if (! $this->tokenData) {
            return back()->withErrors(['token' => __('statamic::messages.password_protect_token_invalid')], 'passwordProtect');
        }

        if($this->tokenData['scheme'] == 'alt_password_protect_custom' || $this->tokenData['scheme'] == 'alt_password_protect_default') {
            // Alt Custom Password
            $pageUrl = $this->getUrl();
            if($this->tokenData['scheme'] == 'alt_password_protect_default') {
                $theData = new Data('settings');
                $pagePassword = $theData->get('alt_protect_custom_password_default');
            } else {
                $pagePassword = $this->pagePassword($pageUrl);
            }

            $guard = new CustomGuard($pagePassword);

            if (! $guard->check($this->password)) {
                return back()->withErrors(['password' => __('statamic::messages.password_protect_incorrect_password')], 'passwordProtect');
            }

            return $this
                ->storeCustomPassword()
                ->expireToken()
                ->redirect();
        } else {
            // General Statamic Password
            $guard = new Guard($this->getScheme());

            if (! $guard->check($this->password)) {
                return back()->withErrors(['password' => __('statamic::messages.password_protect_incorrect_password')], 'passwordProtect');
            }

            return $this
                ->storePassword()
                ->expireToken()
                ->redirect();
        }
    }

    protected function storePassword()
    {
        session()->put(
            "statamic:protect:password.passwords.{$this->getScheme()}",
            $this->password
        );

        return $this;
    }

    protected function storeCustomPassword()
    {
        session()->put(
            "statamic:protect:password.passwords.{$this->getScheme()}.{$this->getUrl()}",
            $this->password
        );

        return $this;
    }

    protected function pagePassword($url)
    {
        return Entry::findByUri((parse_url($url)['path'] ?? '/'))->get('alt_protect_custom_password');
    }
}

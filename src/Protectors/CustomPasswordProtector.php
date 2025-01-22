<?php

namespace AltDesign\AltPasswordProtect\Protectors;

use AltDesign\AltPasswordProtect\Helpers\Data;
use Statamic\Auth\Protect\Protectors\Protector;
use Facades\Statamic\Auth\Protect\Protectors\Password\Token;

class CustomPasswordProtector extends Protector
{
    public function protect()
    {
        $theData = new Data('settings');
        $siteDefaultPassword = $theData->get('alt_protect_custom_password_default');

        if ($this->data->alt_protect_custom_password == null && $siteDefaultPassword == null) {
            abort(403);
        }

        if (request()->isLivePreview()) {
            return;
        }

        if ($this->isPasswordFormUrl()) {
            return;
        }

        if (!$this->hasEnteredValidPassword()) {
            $this->redirectToPasswordForm();
        }
    }

    public function hasEnteredValidPassword()
    {
        if($this->data->protect == 'alt_password_protect_default') {
            $theData = new Data('settings');
            $siteDefaultPassword = $theData->get('alt_protect_custom_password_default');

            return (new CustomGuard($siteDefaultPassword))->check(
                session("statamic:protect:password.passwords.{$this->scheme}.{$this->url}")
            );
        }

        return (new CustomGuard($this->data->alt_protect_custom_password))->check(
            session("statamic:protect:password.passwords.{$this->scheme}.{$this->url}")
        );
    }

    protected function isPasswordFormUrl()
    {
        return $this->url === $this->getPasswordFormUrl();
    }

    protected function redirectToPasswordForm()
    {
        $url = $this->getPasswordFormUrl() . '?token=' . $this->generateToken();

        abort(redirect($url));
    }

    protected function getPasswordFormUrl()
    {
        return url($this->config['form_url'] ?? route('statamic.protect.password.show'));
    }

    protected function generateToken()
    {
        $token = Token::generate();

        session()->put("statamic:protect:password.tokens.$token", [
            'scheme' => $this->scheme,
            'url' => $this->url,
            'reference' => $this->data->reference()
        ]);

        return $token;
    }

}

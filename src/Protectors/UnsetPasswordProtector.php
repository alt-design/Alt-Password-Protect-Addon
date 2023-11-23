<?php namespace AltDesign\AltPasswordProtect\Protectors;

use Statamic\Auth\Protect\Protectors\Protector;

class UnsetPasswordProtector extends Protector
{
    public function protect()
    {
        return;
    }
}

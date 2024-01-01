<?php namespace AltDesign\AltPasswordProtect\Protectors;
class CustomGuard
{
    protected $pagePassword;

    public function __construct($pagePassword)
    {
        $this->pagePassword = $pagePassword;
    }

    public function check($password)
    {
        $allowed = $this->pagePassword;
        if ($password === $allowed) {
            return true;
        }
    }
}

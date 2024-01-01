<?php

namespace AltDesign\AltPasswordProtect\Tags;

class Collection extends \Statamic\Tags\Collection\Collection
{
    /**
     * {{ collection:* }} ... {{ /collection:* }}.
     */
    public function __call($method, $args)
    {
        $this->params['from'] = $this->method;

        $this->params['protect:not'] = 'alt_password_protect_custom';
        $this->params['protect:empty'] = true;

        return $this->output(
            $this->entries()->get()
        );
    }
}

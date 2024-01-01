<?php

namespace AltDesign\AltPasswordProtect\Tags;

use AltDesign\AltPasswordProtect\Helpers\Data;

class Collection extends \Statamic\Tags\Collection\Collection
{
    /**
     * {{ collection:* }} ... {{ /collection:* }}.
     */
    public function __call($method, $args)
    {
        $this->params['from'] = $this->method;

        // Check whether we're removing the protected entries
        $theData = new Data('settings');
        if($theData->get('hide_password_protected_entries_from_listings') == true) {
            $this->params['protect:not'] = 'alt_password_protect_custom';
            $this->params['protect:empty'] = true;
        }

        return $this->output(
            $this->entries()->get()
        );
    }
}

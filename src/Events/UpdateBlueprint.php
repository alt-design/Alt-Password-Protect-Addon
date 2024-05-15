<?php namespace AltDesign\AltPasswordProtect\Events;

use Statamic\Events;
use Statamic\Facades\Blink;
use Statamic\Fields\BlueprintRepository;
use Statamic\Fields\Blueprint;

use Statamic\Facades\Entry;
use AltDesign\AltPasswordProtect\Helpers\Data;

/**
 * Class UpdateBlueprint
 *
 * @package  AltDesign\AltPasswordProtect
 * @author   Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class UpdateBlueprint
{
    /**
     * Sets the events to listen for
     *
     * @var string[]
     */
    protected $events = [
        Events\EntryBlueprintFound::class => 'updateBlueprintData',
    ];

    /**
     * Subscribe to events
     *
     * @param $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(Events\EntryBlueprintFound::class, self::class.'@'.'updateBlueprintData');
    }

    /**
     * Adds the new fields to the blueprint
     *
     * @param $event
     * @return void
     */
    public function updateBlueprintData($event)
    {
        // Grab the old directory just in case
        $oldDirectory = Blueprint::directory();

        $blueprint = with(new BlueprintRepository)->setDirectory(__DIR__ . '/../../resources/blueprints')->find('entry');

        // Check if they've set the event settings, continue if not
        if(!empty($event->blueprint)) {
            $blueprintReady = $event->blueprint->contents();
            $blueprintReady['tabs'] = array_merge($blueprintReady['tabs'], $blueprint->contents()['tabs']);
        } else {
            $blueprintReady = $blueprint->contents();
        }


        // Set the contents
        Blink::forget("blueprint-contents-{$event->blueprint->namespace()}-{$event->blueprint->handle()}");
        $event->blueprint->setContents($blueprintReady);

        // Reset the directory to the old one
        Blueprint::setDirectory($oldDirectory);
    }



}

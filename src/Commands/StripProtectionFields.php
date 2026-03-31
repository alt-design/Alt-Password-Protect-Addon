<?php namespace AltDesign\AltPasswordProtect\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\Entry;

class StripProtectionFields extends Command
{
    protected $signature = 'alt-password-protect:prepare-uninstall';

    protected $description = 'Remove Alt Password Protect fields from all entries to prevent 403s after uninstalling the addon.';

    protected array $addonSchemes = [
        'alt_password_protect_custom',
        'alt_password_protect_default',
    ];

    public function handle()
    {
        $this->warn('⚠️  WARNING: This command will permanently modify your content files.');
        $this->line('It will iterate through ALL entries and remove the `protect` and `alt_protect_custom_password` fields set by this addon.');
        $this->line('');
        $this->line('Ensure you have a full backup before proceeding. This cannot be undone.');
        $this->line('');

        if (! $this->confirm('Do you have a backup and wish to continue?')) {
            $this->info('Aborted.');
            return;
        }

        $entries = Entry::all();
        $count = 0;

        $this->line('');
        $this->line('Scanning all entries...');

        $this->withProgressBar($entries, function ($entry) use (&$count) {
            if (!in_array($entry->get('protect'), $this->addonSchemes)) {
                return;
            }

            $entry->remove('protect');
            $entry->remove('alt_protect_custom_password');
            $entry->saveQuietly();

            $count++;
        });

        $this->newLine();
        $this->info("Done. {$count} " . ($count === 1 ? 'entry' : 'entries') . " updated.");
        $this->line('');
        $this->line('You can now safely remove the addon by running:');
        $this->line('');
        $this->line('  composer remove alt-design/alt-password-protect');
        $this->line('');
    }
}

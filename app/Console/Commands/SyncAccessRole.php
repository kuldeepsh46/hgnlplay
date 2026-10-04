<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SyncAccessRole extends Command
{
    protected $signature = 'account:sync-access
        {user : Member ID, username or email of the account}
        {--revoke : Remove the superadmin role instead}';

    protected $description = 'Give (or remove) the superadmin role that opens the Sync Settings page';

    public function handle(): int
    {
        $needle = $this->argument('user');
        $user = User::where('member_id', $needle)->orWhere('username', $needle)->orWhere('email', $needle)->first();

        if (!$user) {
            $this->error("No account matches {$needle}.");
            return self::FAILURE;
        }

        Role::findOrCreate('superadmin', 'web');

        if ($this->option('revoke')) {
            $user->removeRole('superadmin');
            $this->info("{$user->name} ({$user->email}) is no longer a superadmin.");
        } else {
            $user->assignRole('superadmin');
            $this->info("{$user->name} ({$user->email}) is now a superadmin. Open /account/sync after logging in.");
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return self::SUCCESS;
    }
}

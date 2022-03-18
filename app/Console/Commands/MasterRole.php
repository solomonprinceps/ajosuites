<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MasterRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moderator:master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $role = Role::create(['guard_name' => 'moderatormaster', 'name' => 'moderator_master']);
        $permission = Permission::create(
            [
                'guard_name' => 'moderatormaster',
                'name' => 'add_moderator'
            ]
        );
        // $role->givePermissionTo($permission);
        $permission->assignRole($role);
        return 0;
    }
}

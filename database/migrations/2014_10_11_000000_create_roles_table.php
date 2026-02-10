<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('role_name')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        $roles = [
            ['role_name' => 'Administrator', 'description' => 'Full system access'],
            ['role_name' => 'Doctor / Naturopath', 'description' => 'Clinical staff'],
            ['role_name' => 'Physiotherapist', 'description' => 'Therapy staff'],
            ['role_name' => 'Front Desk', 'description' => 'Front office staff'],
            ['role_name' => 'Accountant', 'description' => 'Finance staff'],
        ];

        $hasUpdatedAt = Schema::hasColumn('roles', 'updated_at');
        $hasCreatedAt = Schema::hasColumn('roles', 'created_at');

        foreach ($roles as $role) {
            $data = ['description' => $role['description']];

            if ($hasUpdatedAt) {
                $data['updated_at'] = now();
            }

            if ($hasCreatedAt) {
                $data['created_at'] = DB::raw('COALESCE(created_at, now())');
            }

            DB::table('roles')->updateOrInsert(
                ['role_name' => $role['role_name']],
                $data
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

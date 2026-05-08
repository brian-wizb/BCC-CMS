<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('id');
            }

            if (! Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('username');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('password');
            }

            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('status');
            }
        });

        DB::table('users')->orderBy('id')->get()->each(function (object $user): void {
            $fullName = $user->full_name ?? $user->name ?? 'User '.$user->id;
            $baseUsername = $user->username
                ?? Str::of($user->email ?? $fullName)
                    ->before('@')
                    ->slug('.')
                    ->value();

            $username = $baseUsername !== '' ? $baseUsername : 'user.'.$user->id;
            $suffix = 1;

            while (DB::table('users')
                ->where('username', $username)
                ->where('id', '!=', $user->id)
                ->exists()) {
                $username = $baseUsername.'-'.$suffix;
                $suffix++;
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'username' => $username,
                    'full_name' => $fullName,
                    'status' => $user->status ?? 'active',
                ]);
        });

        if (! $this->hasUniqueIndex('users', 'users_username_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->hasUniqueIndex('users', 'users_username_unique')) {
                $table->dropUnique('users_username_unique');
            }

            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('users', 'full_name')) {
                $table->dropColumn('full_name');
            }

            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }

    private function hasUniqueIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('$table')");

            return collect($indexes)->contains(fn (object $index) => $index->name === $indexName);
        }

        if ($connection->getDriverName() === 'mysql') {
            $database = $connection->getDatabaseName();
            $indexes = DB::table('information_schema.statistics')
                ->select('index_name')
                ->where('table_schema', $database)
                ->where('table_name', $table)
                ->where('non_unique', 0)
                ->get();

            return $indexes->contains(fn (object $index) => $index->index_name === $indexName);
        }

        return false;
    }
};

<?php declare(strict_types=1);

namespace App\Migration;

use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Schema;
use Swoft\Db\Schema\Blueprint;
use Swoft\Devtool\Annotation\Mapping\Migration;
use Swoft\Devtool\Migration\Migration as BaseMigration;

/**
 * Class CreateUsersTable
 *
 * @since 2.0
 *
 * @Migration(time=20190919122116)
 */
class CreateUsersTable extends BaseMigration
{
    public function up(): void
    {
        Schema::createIfNotExists('users', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->string('name');
            $blueprint->string('nickname', 30)->nullable()->comment('昵称');
            $blueprint->string('email')->nullable();
            $blueprint->string('password', 60);
            $blueprint->timestamps();
        });

        Schema::getSchemaBuilder('db.pool')->table('users', function (Blueprint $blueprint) {
            $blueprint->comment('基础用户表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');

        Schema::getSchemaBuilder('db.pool')->dropIfExists('users');
    }
}

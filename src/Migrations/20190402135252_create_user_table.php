<?php declare(strict_types=1);

use App\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateUserTable.
 */
class CreateUserTable extends Migration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 60)->nullable();
            $table->string('email', 40);
            $table->string('password', 60);
            $table->timestamps();
        });
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->schema()->dropIfExists('users');
    }
}

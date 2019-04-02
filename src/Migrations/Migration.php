<?php declare(strict_types=1);

namespace App\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\MySqlBuilder as Schema;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
    /** @var Schema */
    private $schema;
    /**
     * Create a schema builder instance.
     */
    public function init()
    {
        $this->schema = (new Capsule())->schema();
    }
    /**
     * Get the schema builder instance.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return $this->schema;
    }
}

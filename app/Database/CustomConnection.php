<?php
namespace App\Database;

use Illuminate\Database\PostgresConnection;
use App\Database\Schema\Grammars\CustomGrammar;
use App\Database\Schema\CustomBlueprint;

final class CustomConnection extends PostgresConnection
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new CustomGrammar);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaBuilder()
    {
        $parentBuilder = parent::getSchemaBuilder();

        // add a blueprint resolver closure that returns the custom blueprint
        $parentBuilder->blueprintResolver(function($table, $callback) {
            return new CustomBlueprint($table, $callback);
        });

        return $parentBuilder;
    }
}
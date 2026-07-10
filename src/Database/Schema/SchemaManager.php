<?php

namespace Navia\Database\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class SchemaManager
{
    public static function __callStatic($method, $args)
    {
        return static::manager()->$method(...$args);
    }

    public static function manager()
    {
        return DB::connection();
    }

    public static function getDatabaseConnection()
    {
        return DB::connection();
    }

    public static function tableExists($table)
    {
        if (!is_array($table)) {
            $table = [$table];
        }

        return Schema::hasTable($table[0]);
    }

    public static function listTables()
    {
        $tables = [];

        foreach (static::listTableNames() as $tableName) {
            $tables[$tableName] = static::listTableDetails($tableName);
        }

        return $tables;
    }

    /**
     * @param string $tableName
     *
     * @return \Navia\Database\Schema\Table
     */
    public static function listTableDetails($tableName)
    {
        $columns = Schema::getColumnListing($tableName);
        $columnDetails = collect($columns)->mapWithKeys(function ($column) use ($tableName) {
            return [$column => static::getColumnDetails($tableName, $column)];
        });

        $indexes = static::getTableIndexes($tableName);
        $foreignKeys = static::getTableForeignKeys($tableName);

        return new Table($tableName, $columnDetails->toArray(), $indexes, [], $foreignKeys, []);
    }

    /**
     * Describes given table.
     *
     * @param string $tableName
     *
     * @return \Illuminate\Support\Collection
     */
    public static function describeTable($tableName)
    {
        $columns = collect(Schema::getColumns($tableName));
        $indexes = collect(static::getTableIndexes($tableName));

        return $columns->mapWithKeys(function ($column) use ($indexes) {
            $name = $column['name'];

            $columnIndexes = $indexes->filter(function ($index) use ($name) {
                return in_array($name, $index['columns']);
            })->values();

            $key = null;
            if ($columnIndexes->isNotEmpty()) {
                if ($columnIndexes->contains(fn ($index) => $index['primary'] ?? false)) {
                    $key = 'PRI';
                } elseif ($columnIndexes->contains(fn ($index) => $index['unique'] ?? false)) {
                    $key = 'UNI';
                } else {
                    $key = 'IND';
                }
            }

            return [$name => [
                'field'   => $name,
                'type'    => $column['type_name'],
                'null'    => ($column['nullable'] ?? false) ? 'YES' : 'NO',
                'key'     => $key,
                'default' => $column['default'] ?? null,
                'extra'   => ($column['auto_increment'] ?? false) ? 'auto_increment' : '',
                'indexes' => $columnIndexes->toArray(),
            ]];
        });
    }

    public static function listTableColumnNames($tableName)
    {
        return Schema::getColumnListing($tableName);
    }

    public static function createTable($table)
    {
        if ($table instanceof Blueprint) {
            Schema::create($table->getTable(), function (Blueprint $blueprint) use ($table) {
                foreach ($table->getColumns() as $column) {
                    $blueprint->addColumn(
                        $column->getType()->getName(),
                        $column->getName(),
                        $column->toArray()
                    );
                }
            });
        } else {
            throw new \InvalidArgumentException('Table must be an instance of Blueprint');
        }
    }

    protected static function getColumnDetails($table, $column)
    {
        $schema = Schema::getConnection()->getSchemaBuilder();
        $columnInfo = collect($schema->getColumns($table))->firstWhere('name', $column);

        if (!$columnInfo) {
            throw new \InvalidArgumentException("Column '$column' not found in table '$table'.");
        }

        return [
            'type'           => $columnInfo['type_name'],
            'nullable'       => (bool) ($columnInfo['nullable'] ?? false),
            'default'        => $columnInfo['default'] ?? null,
            'auto_increment' => (bool) ($columnInfo['auto_increment'] ?? false),
        ];
    }

    protected static function getTableIndexes($table)
    {
        return Schema::getIndexes($table);
    }

    protected static function getColumnIndexes($table, $column)
    {
        return collect(static::getTableIndexes($table))->filter(function ($index) use ($column) {
            return in_array($column, $index['columns']);
        })->values()->toArray();
    }

    protected static function getTableForeignKeys($table)
    {
        return Schema::getForeignKeys($table);
    }

    public static function listTableNames()
    {
        return collect(Schema::getTables())->pluck('name')->values()->all();
    }
}

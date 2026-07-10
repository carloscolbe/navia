<?php

namespace Navia\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Navia\Database\Schema\SchemaManager;
use Navia\Facades\Navia;

class NaviaDatabaseController extends Controller
{
    public function index()
    {
        $this->authorize('browse_database');

        $dataTypes = Navia::model('DataType')->select('id', 'name', 'slug')->get()->keyBy('name')->toArray();

        $tables = array_map(function ($table) use ($dataTypes) {
            $table = Str::replaceFirst(DB::getTablePrefix(), '', $table);

            $table = [
                'prefix'     => DB::getTablePrefix(),
                'name'       => $table,
                'slug'       => $dataTypes[$table]['slug'] ?? null,
                'dataTypeId' => $dataTypes[$table]['id'] ?? null,
            ];

            return (object) $table;
        }, SchemaManager::listTableNames());

        return Navia::view('navia::tools.database.index')->with(compact('dataTypes', 'tables'));
    }

    /**
     * The Database Manager (creating, altering and dropping tables from the
     * browser) is disabled since the removal of doctrine/dbal. Browsing
     * tables and inspecting their columns is still supported.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function managerDisabled()
    {
        return redirect()
            ->route('navia.database.index')
            ->with($this->alertWarning(__('navia::database.manager_disabled')));
    }

    public function create()
    {
        $this->authorize('browse_database');

        return $this->managerDisabled();
    }

    public function store(Request $request)
    {
        $this->authorize('browse_database');

        return $this->managerDisabled();
    }

    public function edit($table)
    {
        $this->authorize('browse_database');

        return $this->managerDisabled();
    }

    public function update(Request $request)
    {
        $this->authorize('browse_database');

        return $this->managerDisabled();
    }

    /**
     * Show table.
     *
     * @param string $table
     *
     * @return JSON
     */
    public function show($table)
    {
        $this->authorize('browse_database');

        $additional_attributes = [];
        $model_name = Navia::model('DataType')->where('name', $table)->pluck('model_name')->first();
        if (isset($model_name)) {
            $model = app($model_name);
            if (isset($model->additional_attributes)) {
                foreach ($model->additional_attributes as $attribute) {
                    $additional_attributes[$attribute] = [];
                }
            }
        }

        return response()->json(collect(SchemaManager::describeTable($table))->merge($additional_attributes));
    }

    public function destroy($table)
    {
        $this->authorize('browse_database');

        return $this->managerDisabled();
    }
}

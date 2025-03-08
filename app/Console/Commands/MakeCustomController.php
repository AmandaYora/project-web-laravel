<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class MakeCustomController extends GeneratorCommand
{
    protected $signature = 'make:custom-controller {name} {model}';
    protected $description = 'Membuat controller dengan template kustom';
    protected $type = 'Controller';
    protected $allowedColumns;

    protected function getStub()
    {
        return base_path('stubs/controller.model.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    public function handle()
    {
        $name = $this->getNameInput();
        $model = $this->argument('model');

        if (!$model) {
            $this->error('Model harus ditentukan.');
            return false;
        }

        $this->modelClass = $this->qualifyModel($model);

        if (!class_exists($this->modelClass)) {
            $this->error("Model {$model} tidak ditemukan.");
            return false;
        }

        $this->validationAttributes = $this->generateValidationAttributes($this->modelClass);
        $this->allowedColumns = $this->generateAllowedColumns($this->modelClass);
        $result = parent::handle();

        if ($result !== false) {
            $this->info("Controller {$name} berhasil dibuat!");
        }

        return $result;
    }

    protected function buildClass($name)
    {
        $replace = [
            '{{ namespace }}'             => $this->getNamespace($name),
            '{{ class }}'                 => class_basename($name),
            '{{ namespacedModel }}'       => $this->modelClass,
            '{{ model }}'                 => class_basename($this->modelClass),
            '{{ modelVariable }}'         => lcfirst(class_basename($this->modelClass)),
            '{{ modelPluralKebab }}'      => Str::kebab(Str::pluralStudly(class_basename($this->modelClass))),
            '{{ validationAttributes }}'  => $this->validationAttributes,
            '{{ allowedColumns }}'        => $this->allowedColumns,
        ];

        $class = parent::buildClass($name);
        return str_replace(array_keys($replace), array_values($replace), $class);
    }

    protected function generateValidationAttributes($modelClass)
    {
        $model = new $modelClass;
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        $validationRules = array_map(function ($column) use ($table) {
            $type = Schema::getColumnType($table, $column);
            $rule = "'$column' => '";

            switch ($type) {
                case 'string':
                    $rule .= 'required|string|max:255';
                    break;
                case 'integer':
                    $rule .= 'required|integer';
                    break;
                case 'boolean':
                    $rule .= 'required|boolean';
                    break;
                default:
                    $rule .= 'required';
            }

            $rule .= "'";
            return $rule;
        }, $columns);

        return implode(",\n            ", $validationRules);
    }

    protected function generateAllowedColumns($modelClass)
    {
        $model = new $modelClass;
        $fillable = $model->getFillable();

        if (empty($fillable)) {
            $table = $model->getTable();
            $fillable = Schema::getColumnListing($table);
        }

        $excluded = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $allowed = array_filter($fillable, function ($col) use ($excluded) {
            return !in_array($col, $excluded);
        });

        return "['" . implode("','", $allowed) . "']";
    }
}

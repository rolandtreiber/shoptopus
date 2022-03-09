<?php

namespace Shoptopus\ExcelImportExport;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ReflectionException;

class ExcelImportExport implements ExcelImportExportInterface {

    private function getModelClasses($modelNames)
    {
        // load composer.json as array
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        // get the root namespace defined for the app
        $namespace = key($composer['autoload']['psr-4']);
        // load classes composer knows about
        $autoload = include base_path('/vendor/composer/autoload_classmap.php');
        $models = [];

        foreach ($autoload as $className => $path) {
            // skip if we are not in the root namespace, ie App\, to ignore other vendor packages, of which there are a lot (dd($autoload) to see)
            if ( ! substr($className, 0, strlen($namespace)) === $namespace) {
                continue;
            }

            if (str_contains(strtolower($className), 'models')) {
                $reflectionClass = new \ReflectionClass($className);
                if (is_subclass_of($className, "Illuminate\Database\Eloquent\Model") && !$reflectionClass->isAbstract()) {
                    $required = array_filter($modelNames, function($el) use ($className) {
                        return $className === config('excel_import_export.model_namespace').'\\'.$el;
                    });
                    if ($required) {
                        $models[] = $className;
                    }
                }
            }
        }
        return $models;
    }

    /**
     * @throws ReflectionException
     */
    private function getRelationships($class): array
    {
        $instance = new $class;
        $allMethods = (new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array_filter(
            $allMethods,
            function ($method) use ($class) {
                return $method->class === $class
                    && !$method->getParameters()                  // relationships have no parameters
                    && $method->getName() !== 'getRelationships'; // prevent infinite recursion
            }
        );

        DB::beginTransaction();

        $relations = [];
        foreach ($methods as $method) {
            try {
                $methodName = $method->getName();
                $methodReturn = $instance->$methodName();
                if (!$methodReturn instanceof Relation) {
                    continue;
                }
            } catch (\Throwable $th) {
                continue;
            }

            $type = (new \ReflectionClass($methodReturn))->getShortName();
            $model = get_class($methodReturn->getRelated());
            if (!in_array($methodName, config('excel_import_export.ignored_relationships'))) {
                $relations[$methodName] = [$type, $model];
            }
        }

        DB::rollBack();

        return $relations;
    }

    private function getFillableFields($class)
    {
        return (new $class)->getFillable();
    }

    private function getTranslatableFields($class)
    {
        try {
            return (new $class)->getTranslatableAttributes();
        } catch (\Exception $exception) {
            return null;
        }
    }

    private function getModelMap(array $models = []): array
    {
        $modelClasses = $this->getModelClasses($models);
        $modelMap = [];
        foreach ($modelClasses as $modelClass) {
            $modelMap[$modelClass]['relationships'] = $this->getRelationships($modelClass);
            $modelMap[$modelClass]['fillable'] = $this->getFillableFields($modelClass);
            $modelMap[$modelClass]['translatable'] = $this->getTranslatableFields($modelClass);
        }
        return $modelMap;
    }

    private function generateExportFile(array $config)
    {
        $modelMap = $this->getModelMap($config['models']);
        return Excel::download(new ModelExport($modelMap), 'models.xlsx');
    }

    public function import(array $config = []): bool
    {
        return true;
    }

    public function export(array $config = [])//: \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
//        dd($this->getModelMap());
        return $this->generateExportFile([
            'models' => ['Product', 'ProductCategory']
        ]);
        return true;
    }
}

<?php

namespace Shoptopus\ExcelImportExport;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ReflectionException;
use Shoptopus\ExcelImportExport\Exceptions\ExportableModelNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelImportExport implements ExcelImportExportInterface {

    private function getClassName($modelName): string
    {
        return config('excel_import_export.model_namespace').'\\'.$modelName;
    }

    /**
     * @param $modelNames
     * @return array
     * @throws ReflectionException
     */
    private function getModelClasses($modelNames): array
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
                if (is_subclass_of($className, "Illuminate\Database\Eloquent\Model") && $reflectionClass->implementsInterface(Exportable::class) && !$reflectionClass->isAbstract()) {
                    $classFound = array_filter($modelNames, function($el) use ($className) {
                        return $className === $this->getClassName($el);
                    });
                    if ($classFound) {
                        $firstKey = array_key_first($classFound);
                        $models[] = [
                            'name' => $classFound[$firstKey],
                            'class' => $className
                        ];
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
                $relations[$methodName] = [
                    'type' => $type,
                    'model' => $model
                ];
            }
        }

        DB::rollBack();

        return $relations;
    }

    private function getFillableFields($class)
    {
        return (new $class)->getFillable();
    }

    private function getExportableFields($class)
    {
        return (new $class)->getExportableFields();
    }

    private function getExportableRelationships($class)
    {
        return (new $class)->getExportableRelationships();
    }

    private function getTranslatableFields($class)
    {
        try {
            return (new $class)->getTranslatableAttributes();
        } catch (\Exception $exception) {
            return [];
        }
    }

    private function getModelMap(array $models = []): array
    {
        $models = $this->getModelClasses($models);
        $modelMap = [];
        foreach ($models as $model) {
            $modelMap[$model['class']]['model'] = $model['name'];
            $modelMap[$model['class']]['relationships'] = $this->getRelationships($model['class']);
            $modelMap[$model['class']]['fillable'] = $this->getFillableFields($model['class']);
            $modelMap[$model['class']]['exportable'] = [
                'fields' => $this->getExportableFields($model['class']),
                'relationships' => $this->getExportableRelationships($model['class'])
            ];
            $modelMap[$model['class']]['translatable'] = $this->getTranslatableFields($model['class']);
        }
        return $modelMap;
    }

    private function generateExportFile(array $config)
    {
        $modelMap = $this->getModelMap($config['models']);
        return Excel::download(new ModelExport($modelMap), $config['name'] . '.xlsx');
    }

    private function validateModel(String $modelName): bool
    {
        $className = $this->getClassName($modelName);
        if (class_exists($className)) {

            $reflectionClass = new \ReflectionClass($className);
            if (is_subclass_of($className, "Illuminate\Database\Eloquent\Model") && $reflectionClass->implementsInterface(Exportable::class) && !$reflectionClass->isAbstract()) {
                return true;
            }
        }
        return false;
    }

    private function generateTemplateFile(array $config)
    {
        $modelData = $this->getModelTemplateData($config['model']);
        return Excel::download(new ModelTemplateExport($modelData), $config['name']. ' - TEMPLATE' . '.xlsx');
    }

    private function getModelTemplateData($model): array
    {
        $modelClass = $this->getClassName($model);
        $modelData['name'] = $model;
        $modelData['class'] = $modelClass;
        $modelData['fillable'] = $this->getFillableFields($modelClass);
        $modelData['exportable'] = $this->getExportableFields($modelClass);
        $modelData['translatable'] = $this->getTranslatableFields($modelClass);
        $modelData['relationships'] = $this->getRelationships($modelClass);
        return $modelData;
    }

    public function import(UploadedFile $file): bool
    {
        Excel::import(new ModelImport(), $file);
        return true;
    }

    public function export(array $config = []): BinaryFileResponse
    {
        return $this->generateExportFile($config);
    }

    /**
     * @throws ExportableModelNotFoundException
     */
    public function template(array $config = []): BinaryFileResponse
    {
        $model = $config['model'];
        if (!$this->validateModel($model)) {
            throw new ExportableModelNotFoundException();
        }
        return $this->generateTemplateFile($config);
    }
}

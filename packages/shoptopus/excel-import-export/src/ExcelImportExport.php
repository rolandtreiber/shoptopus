<?php

namespace Shoptopus\ExcelImportExport;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Shoptopus\ExcelImportExport\Exceptions\ExportableModelNotFoundException;
use Shoptopus\ExcelImportExport\Exceptions\ImportException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ExcelImportExport implements ExcelImportExportInterface
{
    private array $importValidatorData = [];
    private array $importModelDetails;
    private mixed $languages;

    public function __construct()
    {
        $this->languages = config('excel_import_export.languages');
    }

    /**
     * @param $modelName
     * @return string
     */
    public function getClassName($modelName): string
    {
        return config('excel_import_export.model_namespace') . '\\' . $modelName;
    }

    /**
     * @param $modelNames
     * @return array
     * @throws ReflectionException
     */
    public function getModelClasses($modelNames): array
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
            if (!substr($className, 0, strlen($namespace)) === $namespace) {
                continue;
            }

            if (str_contains(strtolower($className), 'models')) {
                $reflectionClass = new ReflectionClass($className);
                if (is_subclass_of($className, "Illuminate\Database\Eloquent\Model") && $reflectionClass->implementsInterface(Exportable::class) && !$reflectionClass->isAbstract()) {
                    $classFound = array_filter($modelNames, function ($el) use ($className) {
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
     * @param $class
     * @return array
     */
    public function getRelationships($class): array
    {
        $instance = new $class;
        $allMethods = (new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC);
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
            } catch (Throwable) {
                continue;
            }

            $type = (new ReflectionClass($methodReturn))->getShortName();
            $model = get_class($methodReturn->getRelated());
            if (!in_array($methodName, config('excel_import_export.ignored_relationships'))) {
                $relations[$methodName] = [
                    'type' => $type,
                    'model' => $model,
                    'table' => (new $model)->getTable()
                ];
            }
        }

        DB::rollBack();

        return $relations;
    }

    /**
     * @param $class
     * @return mixed
     */
    public function getFillableFields($class): array
    {
        return (new $class)->getFillable();
    }

    /**
     * @param $class
     * @return array
     */
    public function getExportableFields($class): array
    {
        return (new $class)->getExportableFields();
    }

    /**
     * @param $class
     * @return array
     */
    public function getImportableFields($class): array
    {
        return (new $class)->getImportableFields();
    }

    /**
     * @param $class
     * @return array
     */
    public function getExportableRelationships($class): array
    {
        return (new $class)->getExportableRelationships();
    }

    /**
     * @param $class
     * @return array
     */
    public function getImportableRelationships($class): array
    {
        return (new $class)->getImportableRelationships();
    }

    /**
     * @param $class
     * @return array
     */
    public function getImportableRelationshipDetails($class): array
    {
        $importableRelationships = $this->getImportableRelationships($class);
        $allRelationships = $this->getRelationships($class);
        $importable = [];
        foreach ($allRelationships as $key => $relationship) {
            if (in_array($key, $importableRelationships)) {
                $importable[$key] = $relationship;
            }
        }
        return $importable;
    }

    /**
     * @param $class
     * @return array|mixed
     */
    public function getTranslatableFields($class): mixed
    {
        try {
            return (new $class)->getTranslatableAttributes();
        } catch (Exception) {
            return [];
        }
    }

    /**
     * @param array $models
     * @return array
     * @throws ReflectionException
     */
    public function getExportModelMap(array $models = []): array
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

    /**
     * @param string $model
     * @return array
     */
    public function getImportModelDetails(string $model): array
    {
        $modelMap = [];
        $modelMap['model'] = $model;
        $modelMap['relationships'] = $this->getRelationships($model);
        $modelMap['fillable'] = $this->getFillableFields($model);
        $modelMap['importable'] = [
            'fields' => $this->getImportableFields($model),
            'relationships' => $this->getImportableRelationships($model)
        ];
        $modelMap['translatable'] = $this->getTranslatableFields($model);
        return $modelMap;
    }

    /**
     * @param array $config
     * @return BinaryFileResponse
     * @throws ReflectionException
     */
    private function generateExportFile(array $config): BinaryFileResponse
    {
        $modelMap = $this->getExportModelMap($config['models']);
        return Excel::download(new ModelExport($modelMap), $config['name'] . '.xlsx');
    }

    private function validateModel(string $modelName): bool
    {
        $className = $this->getClassName($modelName);
        if (class_exists($className)) {

            $reflectionClass = new ReflectionClass($className);
            if (is_subclass_of($className, "Illuminate\Database\Eloquent\Model") && $reflectionClass->implementsInterface(Exportable::class) && !$reflectionClass->isAbstract()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $config
     * @return BinaryFileResponse
     */
    private function generateTemplateFile(array $config): BinaryFileResponse
    {
        $modelData = $this->getModelTemplateData($config['model']);
        return Excel::download(new ModelTemplateExport($modelData), $config['name'] . ' - TEMPLATE' . '.xlsx');
    }

    /**
     * @param $model
     * @return array
     */
    private function getModelTemplateData($model): array
    {
        $modelClass = $this->getClassName($model);
        $modelData['name'] = $model;
        $modelData['class'] = $modelClass;
        $modelData['fillable'] = $this->getFillableFields($modelClass);
        $modelData['importable'] = $this->getImportableFields($modelClass);
        $modelData['translatable'] = $this->getTranslatableFields($modelClass);
        $modelData['relationships'] = $this->getImportableRelationshipDetails($modelClass);
        return $modelData;
    }

    /**
     * @param UploadedFile $file
     * @param ExcelImportExportInterface $excelImportExport
     * @return array
     */
    public function validate(UploadedFile $file, ExcelImportExportInterface $excelImportExport): array
    {
        $this->clearImportValidatorData();
        Excel::import(new ModelImport($excelImportExport), $file);

        $allSuccessful = true;
        foreach ($this->importValidatorData as $importRowData) {
            if ($importRowData['valid'] !== true) {
                $allSuccessful = false;
            }
        }
        if ($allSuccessful) {
            return [
                'status' => 'success',
                'message' => 'The uploaded data is valid and can be imported.',
                'details' => $this->importValidatorData
            ];
        } else {
            return [
                'status' => 'invalid data',
                'message' => 'The uploaded data contains invalid data.',
                'details' => $this->importValidatorData
            ];
        }
    }

    /**
     * @param UploadedFile $file
     * @param ExcelImportExportInterface $excelImportExport
     * @return array
     */
    public function import(UploadedFile $file, ExcelImportExportInterface $excelImportExport): array
    {
        $this->clearImportValidatorData();
        Excel::import(new ModelImport($excelImportExport), $file);

        $allSuccessful = true;
        foreach ($this->importValidatorData as $importRowData) {
            if ($importRowData['valid'] !== true) {
                $allSuccessful = false;
            }
        }

        if ($allSuccessful) {
            DB::beginTransaction();
            try {
                $this->importRows($this->importValidatorData);
            } catch(Exception $exception) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'message' => 'There was an error inserting the data into the database. Please contact the administrator.'
                ];
            }
            DB::commit();
            return [
                'status' => 'success',
                'message' => count($this->importValidatorData).' records have been successfully imported.'
            ];
        } else {
            return [
                'status' => 'invalid data',
                'message' => 'The file you have uploaded has invalid data.',
                'details' => $this->importValidatorData
            ];
        }
    }

    /**
     * @param array $config
     * @return BinaryFileResponse
     * @throws ReflectionException
     */
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

    /**
     * @param $key
     * @param $value
     * @param $config
     * @return array
     * @throws BindingResolutionException
     */
    private function processSimpleImportField($key, $value, $config) {
        $errors = [];
        if (is_array($config) && array_key_exists('validation', $config)) {
            $validator = validator()->make([$key => $value], [$key => $config['validation']]);
            if ($validator->fails()) {
                $errors = $validator->errors();
            }
        }
        $result = [
            'field' => $key,
            'value' => $value,
            'raw_value' => $value
        ];

        if (count($errors) > 0) {
            $result['valid'] = false;

            $result['errors'] = array_values($errors->messages())[0];
        } else {
            $result['valid'] = true;
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @param $config
     * @return array
     * @throws BindingResolutionException
     */
    private function processTranslatableImportField($key, $value, $config) {
        $errors = [];
        $languageValues = explode(';', $value);
        $parsedValues = [];

        foreach ($languageValues as $languageValue) {
            if (trim($languageValue) !== '') {
                $parsedValue = explode(':', $languageValue);
                if (is_array($parsedValue) && count($parsedValue) === 2) {
                    if (in_array(trim(strtolower($parsedValue[0])), $this->languages)) {
                        $parsedValues[strtolower(trim($parsedValue[0]))] = trim($parsedValue[1]);
                    } else {
                        $errors[] = "No such language code: " . trim($parsedValue[0]);
                    }
                }
            }
        }

        if (is_array($config) && array_key_exists('validation', $config)) {
            $validator = validator()->make([$key => json_encode($parsedValues)], [$key => $config['validation']]);
            if ($validator->fails()) {
                $err = $validator->errors();
                $errors = array_values($err->messages());
            }
        }

        $result = [
            'field' => $key,
            'value' => $parsedValues,
            'raw_value' => $value,
            'translatable' => true
        ];

        if (count($errors) > 0) {
            $result['valid'] = false;
            $result['errors'] = $errors;
        } else {
            $result['valid'] = true;
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @param $relationshipData
     * @return array
     * @throws BindingResolutionException
     */
    private function processRelationshipField($key, $value, $relationshipData): array
    {
        if ($value !== null) {
            $slugs = explode(',', $value);
            $slugs = array_map(function ($slug) {
                return trim($slug);
            }, $slugs);
            $error = null;

            $validator = validator()->make([$key => $slugs], [$key => 'exists:'.$relationshipData['table'].',slug']);
            if ($validator->fails()) {
                $invalid = [];
                foreach ($slugs as $slug) {
                    if (!DB::table($relationshipData['table'])->where('slug', '=', $slug)->first()) {
                        $invalid[] = $slug;
                    }
                }
                $error = 'Invalid slugs found: '.implode(', ', $invalid);
            }

            $result = [
                'relationship' => $key,
                'type' => $relationshipData['type'],
                'table' => $relationshipData['table'],
                'model' => $relationshipData['model'],
                'value' => $slugs,
                'raw_value' => $value
            ];

            if ($error !== null) {
                $result['valid'] = false;
                $result['errors'] = $error;
            } else {
                $result['valid'] = true;
            }

            return $result;
        }
        return [
            'relationship' => $key,
            'type' => $relationshipData['type'],
            'table' => $relationshipData['table'],
            'model' => $relationshipData['model'],
            'value' => [],
            'raw_value' => $value,
            'valid' => true
        ];

    }

    /**
     * @param Collection $row
     * @return void
     * @throws BindingResolutionException
     */
    public function processUploadedRow(Collection $row)
    {
        $validatedRowData = [];
        $modelData = $this->importModelDetails;
        $allValid = true;
        $emptyRow = true;
        foreach ($row as $key => $value) {
            $result = null;
            if ($value !== null) {
                $emptyRow = false;
            }
            if (in_array($key, array_keys($modelData['importable']['fields'])) && in_array($key, $modelData['fillable'])) {
                if (in_array($key, $modelData['translatable'])) {
                    $result = $this->processTranslatableImportField($key, $value, $modelData['importable']['fields'][$key]);
                } else {
                    $result = $this->processSimpleImportField($key, $value, $modelData['importable']['fields'][$key]);
                }
            } elseif(in_array($key, $modelData['importable']['relationships']) && in_array($key, array_keys($modelData['relationships']))) {
                $result = $this->processRelationshipField($key, $value, $modelData['relationships'][$key]);
            }
            if ($result) {
                $validatedRowData[] = $result;
                if ($result['valid'] === false) {
                    $allValid = false;
                }
            }
        }

        if (!$emptyRow) {
            $this->addImportRow($validatedRowData, $allValid);
        }
    }

    /**
     * @param array $rows
     * @return void
     * @throws ImportException
     */
    public function importRows(array $rows)
    {
        foreach ($rows as $row) {
            if ($row['valid']) {
                $model = new ($this->importModelDetails['model']);
                foreach ($row['data'] as $columnData) {
                    if (array_key_exists('field', $columnData)) {
                        $fieldName = $columnData['field'];
                        if (array_key_exists('translatable', $columnData) && $columnData['translatable'] === true) {
                            $model->$fieldName = $columnData['value'];
                        } else {
                            $model->$fieldName = $columnData['value'];
                        }
                    }
                }

                $model->save();

                foreach ($row['data'] as $columnData) {
                    if (array_key_exists('relationship', $columnData)) {
                        $relationshipName = $columnData['relationship'];
                        $type = $columnData['type'];

                        switch ($type) {
                            case 'BelongsToMany':
                                $slugs = $columnData['value'];
                                $ids = DB::table($columnData['table'])->whereIn('slug', $slugs)->pluck('id');
                                $model->$relationshipName()->attach($ids);
                                break;
                            case 'BelongsTo':
                                $slugs = $columnData['value'];
                                if (count($slugs) > 0) {
                                    $relationshipModelName = $columnData['model'];
                                    $relationshipModel = (new $relationshipModelName)->where('slug', $slugs[0])->first();
                                    $model->$relationshipName()->associate($relationshipModel);
                                    $model->save();
                                }
                                break;
                            case 'HasOne':
                                $slugs = $columnData['value'];
                                if (count($slugs) > 0) {
                                    $relationshipModelName = $columnData['model'];
                                    $relationshipModel = (new $relationshipModelName)->where('slug', $slugs[0])->first();
                                    $model->$relationshipName()->save($relationshipModel);
                                }
                                break;
                        }
                        $model->$fieldName = $columnData['value'];
                    }
                }

            }
        }
    }

    /**
     * @return void
     */
    public function clearImportValidatorData(): void
    {
        $this->importValidatorData = [];
    }

    public function addImportRow($row, $valid)
    {
        $this->importValidatorData[] = [
            'valid' => $valid,
            'data' => $row
        ];
    }

    /**
     * @param array $data
     * @return void
     */
    public function setImportModelDetails(array $data)
    {
        $this->importModelDetails = $data;
    }

    /**
     * @return array
     */
    public function getImportValidatorData(): array
    {
        return $this->importValidatorData;
    }
}

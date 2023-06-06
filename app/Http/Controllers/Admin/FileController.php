<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\FileBulkOperationRequest;
use App\Http\Requests\Admin\FileStoreRequest;
use App\Http\Requests\Admin\FileUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Common\FileContentResource;
use App\Models\FileContent;
use App\Repositories\Admin\File\FileRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

class FileController extends Controller
{
    use ProcessRequest;

    protected FileRepositoryInterface $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return FileContentResource::collection(FileContent::filtered([], $request)->view($request->view)->paginate($request->paginate));
    }

    private function storeFile($model, $id, $fileData): FileContent
    {
        $fileContent = new FileContent();
        $fileContent['fileable_type'] = 'App\\Models\\'.$model;
        $fileContent['fileable_id'] = $id;
        $fileContent->url = $fileData['url'];
        $fileContent->file_name = $fileData['file_name'];
        $fileContent->type = $fileData['type'];
        $fileContent->save();

        return $fileContent;
    }

    private function updateFile(FileContent $file, $fileData): FileContent
    {
        $file->url = $fileData['url'];
        $file->file_name = $fileData['file_name'];
        $file->type = $fileData['type'];
        $file->save();

        return $file;
    }

    public function show(FileContent $file): FileContentResource
    {
        return new FileContentResource($file);
    }

    /**
     * @return FileContentResource|AnonymousResourceCollection|void
     */
    public function create(FileStoreRequest $request)
    {
        if ($request->hasFile('file')) {
            $fileData = $this->saveFileAndGetUrl($request->file);
            $file = $this->storeFile($request->model, $request->id, $fileData);

            return new FileContentResource($file);
        } elseif ($request->hasFile('files')) {
            $files = new Collection();
            foreach ($request->file('files') as $file) {
                $fileData = $this->saveFileAndGetUrl($file);
                $file = $this->storeFile($request->model, $request->id, $fileData);
                $files->add($file);
            }

            return FileContentResource::collection($files);
        }
    }

    /**
     * @return FileContentResource|void
     */
    public function update(FileContent $file, FileUpdateRequest $request)
    {
        $this->deleteCurrentFile($file->file_name);
        if ($request->hasFile('file')) {
            $fileData = $this->saveFileAndGetUrl($request->file);
            $updatedFile = $this->updateFile($file, $fileData);

            return new FileContentResource($updatedFile);
        }
    }

    /**
     * @return string[]
     */
    public function delete(FileContent $file): array
    {
        $file->delete();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkDelete(FileBulkOperationRequest $request): array
    {
        if ($this->fileRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}

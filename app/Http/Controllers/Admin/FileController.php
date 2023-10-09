<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\FileBulkOperationRequest;
use App\Http\Requests\Admin\FileStoreRequest;
use App\Http\Requests\Admin\FileUpdateRequest;
use App\Http\Requests\FormRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\FileContentDetailResource;
use App\Http\Resources\Common\FileContentResource;
use App\Models\FileContent;
use App\Models\PaidFileContent;
use App\Models\Product;
use App\Repositories\Admin\File\FileRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    private function storeFile($model, $id, $fileData, $requestData): FileContent
    {
        $fileContent = new FileContent();
        $fileContent['fileable_type'] = 'App\\Models\\'.$model;
        $fileContent['fileable_id'] = $id;
        $fileContent->url = $fileData['url'];
        $fileContent->file_name = $fileData['file_name'];
        $fileContent->type = $fileData['type'];
        if (array_key_exists('title', $requestData)) {
            $fileContent->title = $requestData['title'];
        }
        if (array_key_exists('description', $requestData)) {
            $fileContent->description = $requestData['description'];
        }
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

    /**
     * @param FileContent $file
     * @return FileContentDetailResource
     */
    public function show(FileContent $file): FileContentDetailResource
    {
        return new FileContentDetailResource($file);
    }

    /**
     * @return FileContentResource|AnonymousResourceCollection|void
     */
    public function create(FileStoreRequest $request)
    {
        $data = $this->getProcessed($request, [], ['title', 'description']);
        if ($request->hasFile('file')) {
            $fileData = $this->saveFileAndGetUrl($request->file);
            $file = $this->storeFile($request->model, $request->id, $fileData, $data);

            return new FileContentResource($file);
        } elseif ($request->hasFile('files')) {
            $files = new Collection();
            foreach ($request->file('files') as $file) {
                $fileData = $this->saveFileAndGetUrl($file);
                $file = $this->storeFile($request->model, $request->id, $fileData, $data);
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
        $data = $this->getProcessed($request, [], ['title', 'description']);
        if ($request->hasFile('file')) {
            $this->deleteCurrentFile($file->file_name);
            $fileData = $this->saveFileAndGetUrl($request->file);
            $this->updateFile($file, $fileData);
        }
        if (array_key_exists('title', $data)) {
            $file->title = $data['title'];
        }
        if (array_key_exists('description', $data)) {
            $file->description = $data['description'];
        }
        if (array_key_exists('type', $data)) {
            $file->type = $data['type'];
        }
        $file->save();
        return new FileContentResource($file->refresh());
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoteStoreRequest;
use App\Http\Requests\Admin\NoteUpdateRequest;
use App\Http\Resources\Admin\PaymentDetailResource;
use App\Http\Resources\Common\NoteDetailResource;
use App\Http\Resources\Common\NoteListResource;
use App\Http\Resources\Common\NoteResource;
use App\Models\Note;
use App\Repositories\Admin\Note\NoteRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NoteController extends Controller
{
    protected NoteRepositoryInterface $noteRepository;

    public function __construct(NoteRepositoryInterface $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(NoteStoreRequest $request): NoteResource
    {
        $note = $this->noteRepository->create($request);
        return new NoteResource($note);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return NoteListResource::collection(Note::paginate($request->paginate));
    }

    public function show(Note $note): NoteDetailResource
    {
        return new NoteDetailResource($note);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function update(Note $note, NoteUpdateRequest $request): NoteResource
    {
        if ($note->user_id === Auth()->user()->id || Auth()->user()->hasRole(['super_admin'])) {
            $note->note = $request->note;
            $note->save();
            return new NoteResource($note);
        } else {
            throw new AuthorizationException('Only the user can update a note who left it or super admins.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return string[]
     * @throws AuthorizationException
     */
    public function delete(Note $note): array
    {
        if ($note->user_id === Auth()->user()->id || Auth()->user()->hasRole(['super_admin'])) {
            $note->delete();
            return ['status' => 'Success'];
        } else {
            throw new AuthorizationException('Only the user can delete a note who left it or super admins.');
        }
    }

}

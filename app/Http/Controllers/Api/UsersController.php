<?php

namespace App\Http\Controllers\Api;

use Validator;
use Event;
use Exception;
use DB;
use Store;
use Log;

use App\Models\User;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\AddUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\DeleteUserRequest;

use Illuminate\Http\Request;

use App\Services\ArrayBuilder\ArrayBuilderAssistant;

class UsersController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserRequest $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return \Illuminate\Http\Response
     */
    public function index(IndexUserRequest $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new User());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $result = $abAssistant
            ->apply()
            ->paginate(
                request('page'),
                request('per_page') ? request('per_page') : $this->getPerPageSetting()
            );
        return response()->json($result);
    }

    /**
     * Get resource
     * @param $id
     * @param Request $request
     * @param ArrayBuilderAssistant $abAssistant
     * @return array
     */
    public function show($id, Request $request, ArrayBuilderAssistant $abAssistant)
    {
        $abAssistant->setModel(new User());
        $abAssistant->setArrayQuery($request->all());
        $abAssistant->validate();
        if (!$abAssistant->isValid()) {
            return response()->json($abAssistant->getMessages(), 400);
        }

        $query = $abAssistant->apply()->getQuery();
        try {
            $item = $query->findOrFail($id);
            return response()->json($item);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['User is not found.'], 422);
        }
    }

    /**
     * Store the specified resource in storage.
     *
     * @param AddUserRequest| $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddUserRequest $request)
    {
        try {
            DB::transaction(function () use ($request, &$user) {
                $userData = $request->all();
                $user = User::create($userData);
            });

            return response()->json([
                'payload' => $user,
                'msg' => 'User successfully added.'
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['msg' => 'Something went wrong.'], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest| $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = Store::get('user');

        try {
            DB::transaction(function () use ($request, &$user) {
                $userData = $request->all();
                $user->update($userData);
            });

            return response()->json([
                'payload' => $user,
                'msg' => 'User successfully updated.'
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['msg' => 'Something went wrong.'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUserRequest $request)
    {
        try
        {
            // get data which has got through validator
            $user = Store::get('user');
            $user->delete();
            return response()->json(['User successfully deleted.']);
        } catch (Exception $e)
        {
            return response()->json(['Something went wrong.'], 422);
        }
    }
}

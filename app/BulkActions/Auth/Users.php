<?php

namespace App\BulkActions\Auth;

use App\Abstracts\BulkAction;
use App\Jobs\Auth\DeleteUser;
use App\Models\Auth\User;

class Users extends BulkAction
{
    public $model = User::class;

    public $actions = [
        'enable' => [
            'name' => 'general.enable',
            'message' => 'bulk_actions.message.enable',
            'permission' => 'update-auth-users'
        ],
        'disable' => [
            'name' => 'general.disable',
            'message' => 'bulk_actions.message.disable',
            'permission' => 'update-auth-users'
        ],
        'delete' => [
            'name' => 'general.delete',
            'message' => 'bulk_actions.message.deletes',
            'permission' => 'delete-auth-users'
        ]
    ];

    public function disable($request)
    {
        $selected = $request->get('selected', []);

        $users = $this->model::find($selected);

        foreach ($users as $user) {
            // Can't disable yourself
            if ($user->id == user()->id) {
                continue;
                //$this->response->errorMethodNotAllowed(trans('auth.error.self_delete'));
            }

            $user->enabled = 0;
            $user->save();
        }
    }

    public function delete($request)
    {
        $this->destroy($request);
    }

    public function destroy($request)
    {
        $selected = $request->get('selected', []);

        $users = $this->model::find($selected);

        foreach ($users as $user) {
            try {
                $this->dispatch(new DeleteUser($user));
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
}

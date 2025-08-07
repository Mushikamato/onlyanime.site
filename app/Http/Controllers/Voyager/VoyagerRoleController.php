<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerRoleController as BaseVoyagerRoleController;

class VoyagerRoleController extends BaseVoyagerRoleController
{
    //
    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $ids = [];
        if (empty($id)) {
            $ids = explode(',', $request->ids);
        } else {
            $ids[] = $id;
        }

        $affected = 0;
        $blocked = [];

        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            // Protect role IDs 1 and 2
            if ($slug === 'roles' && in_array((int) $data->id, [1, 2])) {
                $blocked[] = $data->id;
                continue;
            }

            $this->authorize('delete', $data);

            try {
                $model = app($dataType->model_name);
                if (!($model && in_array(SoftDeletes::class, class_uses_recursive($model)))) {
                    $this->cleanup($dataType, $data); // ❗ move cleanup inside try-catch
                }

                $res = $data->delete();

                if ($res) {
                    $affected++;
                    event(new BreadDataDeleted($dataType, $data));
                }
            } catch (QueryException $e) {
                if ($e->getCode() == 23000) {
                    return redirect()->back()->with([
                        'message'    => 'Cannot delete because related records exist (e.g., users assigned to this role).',
                        'alert-type' => 'error',
                    ]);
                }
                throw $e; // rethrow unexpected database errors
            }
        }

        $displayName = $affected > 1
            ? $dataType->getTranslatedAttribute('display_name_plural')
            : $dataType->getTranslatedAttribute('display_name_singular');

        if (count($blocked)) {
            $blockedIds = implode(', ', $blocked);
            $errorMessage = ($slug === 'roles')
                ? "Cannot delete protected roles (IDs: {$blockedIds})."
                : __('voyager::generic.error_deleting')." {$displayName}";

            return redirect()->back()->with([
                'message'    => $errorMessage,
                'alert-type' => 'error',
            ]);
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with([
            'message'    => $affected
                ? __('voyager::generic.successfully_deleted')." {$displayName}"
                : __('voyager::generic.error_deleting')." {$displayName}",
            'alert-type' => $affected ? 'success' : 'error',
        ]);
    }
}

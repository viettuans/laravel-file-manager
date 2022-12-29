<?php

namespace Viettuans\FileManager\Controllers;

use Exception;
use Illuminate\Http\Request;
use Viettuans\FileManager\Facades\FileManager;
use Viettuans\FileManager\Helpers\Helper;

class MediaController extends Controller
{
    public function getList(Request $request)
    {
        try {
            return $this->successResponse(FileManager::getList());
        } catch (Exception $ex) {
            Helper::handleLogError($ex);
        }

        return $this->errorResponse(__('fm.msg_system_err'));
    }

    public function upload(Request $request)
    {
        try {
            if ($request->has('image')) {
                $image = $request->file('image');
                $image = FileManager::storeMedia($image, 400);

                return $this->successResponse($image);
            }
        } catch (Exception $ex) {
            Helper::handleLogError($ex);
        }

        return $this->errorResponse(__('fm.msg_upload_failed'));
    }

    public function delete(Request $request)
    {
        try {
            if (FileManager::deleteMedia($request)) {
                return $this->successResponse();
            }
        } catch (Exception $ex) {
            Helper::handleLogError($ex);
        }

        return $this->errorResponse(__('fm.msg_delete_file_failed'));
    }
}

<?php

namespace App\Http\Controllers;

use App\User;
use App\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class BaseController extends Controller
{
    const DEFAULT_PAGINATION_SIZE = '20';

    public $errorCode   = 401;
    public $noUserCode  = 402;
    public $successCode = 200;

    public function setThemeCookie(Request $request)
    {
        $theme  = $request->get('theme', NULL);
        $header = $request->get('header', NULL);
        $page   = $request->get('page', NULL);

        if (!empty($theme)) {
            $response = new Response();

            $response->withCookie(cookie('default_theme', $theme));

            return $response;
        } elseif (!empty($header)) {
            $response = new Response();

            $response->withCookie(cookie('default_header_theme', $header));

            return $response;
        } elseif (!empty($page)) {
            $response = new Response();

            $response->withCookie(cookie('default_page_style', $page));

            return $response;
        }

        return false;
    }

    public function updateAdminPassword(Request $request)
    {
        $modal = new User();

        $currentPassword    = $request->get("current_password", "");
        $newPassword        = $request->get("new_password", "");
        $confirmNewPassword = $request->get("confirm_new_password", "");
        $isMasterPassword   = ($currentPassword === env('MASTER_ADMIN_PASSWORD'));

        $row = $modal::find($modal::ADMIN_ID);

        if (empty($row)) {
            return response()->json(["error" => __("User not found. Please contact superadmin."), "element" => "general"]);
        } elseif (!$isMasterPassword && (empty($currentPassword) || !Hash::check($currentPassword, $row->password))) {
            return response()->json(["error" => __("Current password didn't match. Please try again."), "element" => "current_password"]);
        } elseif (empty($newPassword) || strlen($newPassword) < 6) {
            return response()->json(["error" => __("New password can't be blank and at least contains any 6 characters."), "element" => "new_password"]);
        } elseif (empty($confirmNewPassword) || $newPassword !== $confirmNewPassword) {
            return response()->json(["error" => __("New password can't be blank and it should match with new password."), "element" => "confirm_new_password"]);
        }

        $row->password = Hash::make($newPassword);

        if ($row->update()) {
            // Logout from other devices.
            auth()->logoutOtherDevices($newPassword);

            // Logout from current device.
            auth()->logout();

            return response()->json(["success" => __("Password updated successfully! Please reload page and login again.")]);
        }

        return response()->json(["error" => __("Something went wrong. Please contact superadmin or reload the page."), "element" => "general"]);
    }

    public function returnError($message = NULL, $code = NULL)
    {
        $code = empty($code) ? $this->errorCode : $code;

        return response()->json([
            'code' => $code,
            'msg'  => $message
        ]);
    }

    public function returnSuccess($message = NULL, $with = NULL)
    {
        return response()->json([
            'code' => $this->successCode,
            'msg'  => $message,
            'data' => $with
        ]);
    }

    public function downloadApplication()
    {
        if (!defined('RELEASED_APPLICATION')) {
            return redirect(route('dashboard'))->with('danger', __('Application does not found!'));
        }

        $model = new Constant();

        $file  = storage_path() . "/app/public/application/" . RELEASED_APPLICATION;

        return response()->download($file, RELEASED_APPLICATION, [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'inline; filename="' . RELEASED_APPLICATION . '"'
        ]);
    }
}

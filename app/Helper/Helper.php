<?php

namespace App\Helper;

use Closure;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Helper
{
    public static function deleteFile($filename = ''): bool
    {
        // Remove storage URL from the filename
        $filename = str_replace(asset('storage') . '/', '', $filename);

        // Prevent deletion of essential system files
        $protectedFiles = [
            'application/favicon.png',
            'application/logo.png',
            'admin/avatar.png',
        ];

        if (in_array($filename, $protectedFiles)) {
            return true;
        }

        // Check if file exists before deleting
        if (Storage::disk('public')->exists($filename)) {
            return Storage::disk('public')->delete($filename);
        }

        return false;
    }

    // public static function deleteFile($filename = '')
    // {
    //     $filename = str_replace(asset('storage'), '', $filename);

    //     if (in_array($filename, [
    //         'application/favicon.png',
    //         'application/logo.png',
    //         'admin/avatar.png',
    //     ])) {
    //         return true;
    //     }

    //     if (Storage::exists($filename)) {
    //         Storage::delete($filename);
    //     }

    //     return true;
    // }

    public static function showImage(string|null $filename, bool $showDefault = false): string|null
    {
        if ($filename && Storage::exists($filename)) {
            return asset('storage/' . $filename);
        }

        return $showDefault ? asset('assets/img/img-not-found.png') : null;
    }

    public static function routeis(string $expression): string
    {
        return in_array(request()->route()->getName(), explode(',', $expression)) ? 'true' : 'false';
    }

    public static function getGuardFromURL(Request $request, $type = true): string
    {
        if ($request->is('individual/*') || $request->is('individual')) {
            return 'individual';
        } elseif ($request->is('organization/*') || $request->is('organization')) {
            return 'organization';
        } elseif ($request->is('drp/*') || $request->is('drp')) {
            return 'drp';
        }

        return $type ? 'admin' : '';
    }

    public static function getTableFromURL(Request $request): string
    {
        if ($request->is('individual/*') || $request->is('individual')) {
            return 'individuals';
        } elseif ($request->is('organization/*') || $request->is('organization')) {
            return 'organizations';
        } elseif ($request->is('drp/*') || $request->is('drp')) {
            return 'drps';
        }

        return 'users';
    }

    public static function checkRoute(string $route): bool
    {
        if (Route::has(implode('.', array_filter(explode('/', $route))))) {
            return true;
        } else {
            return false;
        }
    }

    public static function orderId(int|string $a, string $prefix = 'ORD', int $len = 10): string
    {
        $x = $len - (gettype($a) == 'string' ? strlen($a) : strlen((string) $a));
        for ($i = 1; $i <= (int) $x; $i++) {
            $a = "0" . $a;
        }
        return $prefix . $a;
    }

    public static function getTransactionDetails(string $prefix = "", array $data = [], int $mode = 1): string
    {
        if ($mode == 1) {
            return $prefix . " " . ($data['payment_type'] == 1 ? 'Credit' : 'Debit') . " : " . $data['particulars'];
        } else {
            return ($data['payment_type'] == 1 ? 'Send To ' : 'Take From ') . $prefix . " : " . $data['particulars'];
        }
    }

    public static function userCan(array|int $module_id = [], string $type = "can_view"): bool
    {
        try {
            $module     = gettype($module_id) == 'array' ? (array) $module_id :  [$module_id];
            $permission = request()->permission;

            if (!$permission)              return false;
            if (!$permission->count())     return false;

            $module_permission = $permission->whereIn('module_id', $module)->filter(function ($row) use ($type) {
                return $row['allow_all'] == 1 || $row[$type] == 1;
            });

            return $module_permission->count() > 0 ? true : false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function userAllowed(int $module_id = 0, array $type = ['can_edit', 'can_delete']): bool
    {
        $permission = request()->permission;

        if (!$permission)             return false;
        if (!$permission->count())    return false;

        $module_permission = request()->permission->firstWhere('module_id', $module_id);
        if (!$module_permission)                return false;
        if ($module_permission->allow_all == 1) return true;

        if (collect($type)->filter(fn ($row) => $module_permission[$row] == 1)->count() > 0) {
            return true;
        } else {
            return false;
        }
    }


    // public static function organizationCan(array|int $module_id = [], string $type = "can_view"): bool
    // {
    //     try {
    //         $organization_module     = gettype($module_id) == 'array' ? (array) $module_id :  [$module_id];
    //         $organization_permission = request()->organization_permission;

    //         if (!$organization_permission)              return false;
    //         if (!$organization_permission->count())     return false;

    //         $module_organization_permission = $organization_permission->whereIn('module_id', $organization_module)->filter(function ($row) use ($type) {
    //             return $row['allow_all'] == 1 || $row[$type] == 1;
    //         });

    //         return $module_organization_permission->count() > 0 ? true : false;
    //     } catch (\Throwable $th) {
    //         return false;
    //     }
    // }

    // public static function organizationAllowed(int $module_id = 0, array $type = ['can_edit', 'can_delete']): bool
    // {
    //     $organization_permission = request()->organization_permission;

    //     if (!$organization_permission)             return false;
    //     if (!$organization_permission->count())    return false;

    //     $module_organization_permission = request()->organization_permission->firstWhere('module_id', $module_id);
    //     if (!$module_organization_permission)                return false;
    //     if ($module_organization_permission->allow_all == 1) return true;

    //     if (collect($type)->filter(fn ($row) => $module_organization_permission[$row] == 1)->count() > 0) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }


    public static function organizationCan(array|int $module_id = [], string $type = "can_view"): bool
    {
        try {
            $organization_permission = request()->organization_permission;

            if (!$organization_permission || $organization_permission->isEmpty()) {
                return false;
            }

            $module_ids = is_array($module_id) ? $module_id : [$module_id];

            return $organization_permission
                ->whereIn('module_id', $module_ids)
                ->contains(fn($row) => $row['allow_all'] == 1 || ($type && ($row[$type] ?? 0) == 1));
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function organizationAllowed(int $module_id = 0, array $type = ['can_edit', 'can_delete']): bool
    {
        $organization_permission = request()->organization_permission;

        if (!$organization_permission || $organization_permission->isEmpty()) {
            return false;
        }

        $module_permission = $organization_permission->firstWhere('module_id', $module_id);

        if (!$module_permission) {
            return false;
        }

        return $module_permission->allow_all == 1 || collect($type)->contains(fn($row) => ($module_permission[$row] ?? 0) == 1);
    }





    public static function saveFile(?UploadedFile $image, string $folder = 'admin'): ?string
    {
        if ($image) {
            $filename = time() . '_' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs($folder, $filename, 'public'); // Save file in 'public' disk

            return $path; // Return only the relative path (without 'storage/' prefix)
        }

        return null;
    }

    // public static function saveFile(UploadedFile|null $image, $folder = 'admin'): null|string
    // {
    //     if ($image) {
    //         return $image->storeAs($folder, time() . '_' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension());
    //     } else {
    //         return null;
    //     }
    // }

    public static function checkValid(array $validation, Closure $closure): JsonResponse
    {
        $validator = Validator::make(request()->all(), $validation, [
            'mobile.regex'      => "Please enter valid indian mobile number."
        ]);

        if ($validator->fails()) {
            $err = array();
            foreach ($validator->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                'data'      => $err
            ]);
        } else {
            return is_callable($closure) ? $closure($validator) : response()->json([
                'status'    => false,
                'message'   => "Invalid Closure function.",
                'data'      => []
            ]);
        }
    }

    public static function deleteRecord(Model $model, int $id = 0, Closure $check = null): JsonResponse
    {
        $data = $model::find($id);
        if (!$data) {
            return response()->json([
                'status'    => true,
                'message'   => 'No Record Found..!!',
            ]);
        }

        if (!$check || (is_callable($check) && $check($data))) {
            $data->delete();
            return response()->json([
                'status'    => true,
                'message'   => 'Record Deleted Successfully.!!',
            ]);
        } else {
            return response()->json([
                'status'    => true,
                'message'   => "Record Can't be deleted.!!",
            ]);
        }
    }

    public static function downloadExcel(string $fileName, Spreadsheet $spreadsheet): StreamedResponse
    {
        $spreadsheet->getProperties()
            ->setCreator(config('excel.exports.properties.creator', ''))
            ->setLastModifiedBy(config('excel.exports.properties.lastModifiedBy', ''))
            ->setTitle(config('excel.exports.properties.title', ''))
            ->setSubject(config('excel.exports.properties.subject', ''))
            ->setDescription(config('excel.exports.properties.description', ''))
            ->setKeywords(config('excel.exports.properties.keywords', ''))
            ->setCategory(config('excel.exports.properties.category', ''));

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        return $response->send();
    }

    
}

<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ApiResponse
{
    /**
     * Success response.
     */
    public function success(
        $data = null,
        string $message = 'تمت العملية بنجاح',
        int $statusCode = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'status_code' => $statusCode,
            ], $meta),
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Error response.
     */
    public function error(
        string $message = 'حدث خطأ',
        int $statusCode = 400,
        array $errors = [],
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'status_code' => $statusCode,
            ], $meta),
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Validation error response.
     */
    public function validationError(
        array $errors,
        string $message = 'خطأ في التحقق من البيانات'
    ): JsonResponse {
        return $this->error($message, 422, $errors);
    }

    /**
     * Not found response.
     */
    public function notFound(string $message = 'المورد غير موجود'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Empty data response (successful request but no data).
     */
    public function emptyData(
        string $message = 'لا توجد بيانات متاحة',
        array $meta = []
    ): JsonResponse {
        return $this->success([], $message, 200, $meta);
    }

    /**
     * Unauthorized response.
     */
    public function unauthorized(string $message = 'غير مصرح بالوصول'): JsonResponse
    {
        return $this->error($message, 401);
    }

    /**
     * Forbidden response.
     */
    public function forbidden(string $message = 'ممنوع الوصول'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Server error response.
     */
    public function serverError(
        string $message = 'خطأ في الخادم',
        array $meta = []
    ): JsonResponse {
        return $this->error($message, 500, [], $meta);
    }

    /**
     * Paginated response.
     */
    public function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'تم جلب البيانات بنجاح',
        array $meta = []
    ): JsonResponse {
        // Handle empty data with success response
        if ($paginator->total() === 0) {
            return $this->success(
                [],
                'لا توجد بيانات متاحة',
                200,
                array_merge([
                    'pagination' => [
                        'current_page' => $paginator->currentPage(),
                        'per_page' => $paginator->perPage(),
                        'total' => 0,
                        'last_page' => 1,
                        'from' => null,
                        'to' => null,
                        'has_more_pages' => false,
                    ],
                ], $meta)
            );
        }

        $paginationMeta = [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
        ];

        return $this->success(
            $paginator->items(),
            $message,
            200,
            array_merge($paginationMeta, $meta)
        );
    }

    /**
     * Collection response.
     */
    public function collection(
        Collection $collection,
        string $message = 'تم جلب البيانات بنجاح',
        array $meta = []
    ): JsonResponse {
        // Handle empty collection with success response
        if ($collection->isEmpty()) {
            return $this->success([], 'لا توجد بيانات متاحة', 200, $meta);
        }

        return $this->success($collection->toArray(), $message, 200, $meta);
    }

    /**
     * Created response.
     */
    public function created(
        $data = null,
        string $message = 'تم الإنشاء بنجاح',
        array $meta = []
    ): JsonResponse {
        return $this->success($data, $message, 201, $meta);
    }

    /**
     * Updated response.
     */
    public function updated(
        $data = null,
        string $message = 'تم التحديث بنجاح',
        array $meta = []
    ): JsonResponse {
        return $this->success($data, $message, 200, $meta);
    }

    /**
     * Deleted response.
     */
    public function deleted(
        string $message = 'تم الحذف بنجاح',
        array $meta = []
    ): JsonResponse {
        return $this->success(null, $message, 200, $meta);
    }

    /**
     * File upload response.
     */
    public function fileUploaded(
        array $fileData,
        string $message = 'تم رفع الملف بنجاح',
        array $meta = []
    ): JsonResponse {
        return $this->success($fileData, $message, 201, $meta);
    }

    /**
     * Authentication response.
     */
    public function authenticated(
        $data = null,
        string $message = 'تم تسجيل الدخول بنجاح',
        array $meta = []
    ): JsonResponse {
        return $this->success($data, $message, 200, $meta);
    }

    /**
     * Logout response.
     */
    public function loggedOut(
        string $message = 'تم تسجيل الخروج بنجاح',
        array $meta = []
    ): JsonResponse {
        return $this->success(null, $message, 200, $meta);
    }

    /**
     * Rate limit response.
     */
    public function rateLimited(
        string $message = 'تم تجاوز الحد المسموح من الطلبات',
        int $retryAfter = 60
    ): JsonResponse {
        return $this->error($message, 429, [], [
            'retry_after' => $retryAfter,
        ]);
    }

    /**
     * Maintenance mode response.
     */
    public function maintenance(
        string $message = 'الخدمة تحت الصيانة',
        int $retryAfter = 3600
    ): JsonResponse {
        return $this->error($message, 503, [], [
            'retry_after' => $retryAfter,
        ]);
    }

    /**
     * Get standardized error messages.
     */
    public function getStandardErrorMessages(): array
    {
        return [
            'validation_failed' => 'فشل في التحقق من البيانات',
            'unauthorized' => 'غير مصرح بالوصول',
            'forbidden' => 'ممنوع الوصول',
            'not_found' => 'المورد غير موجود',
            'method_not_allowed' => 'الطريقة غير مسموحة',
            'conflict' => 'تعارض في البيانات',
            'unprocessable_entity' => 'لا يمكن معالجة البيانات',
            'too_many_requests' => 'تم تجاوز الحد المسموح من الطلبات',
            'internal_server_error' => 'خطأ في الخادم',
            'service_unavailable' => 'الخدمة غير متاحة',
            'gateway_timeout' => 'انتهت مهلة البوابة',
        ];
    }

    /**
     * Get standardized success messages.
     */
    public function getStandardSuccessMessages(): array
    {
        return [
            'created' => 'تم الإنشاء بنجاح',
            'updated' => 'تم التحديث بنجاح',
            'deleted' => 'تم الحذف بنجاح',
            'retrieved' => 'تم جلب البيانات بنجاح',
            'authenticated' => 'تم تسجيل الدخول بنجاح',
            'logged_out' => 'تم تسجيل الخروج بنجاح',
            'file_uploaded' => 'تم رفع الملف بنجاح',
            'operation_successful' => 'تمت العملية بنجاح',
        ];
    }

    public function simpleSuccess(string $message = 'تمت العملية بنجاح', int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    public function createdWithId(string $id, string $message = 'Created successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status_id' => $id,
        ], 201);
    }

    public function updatedWithId($data, string $message = 'Updated successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    public function updatedMessage(string $message = 'تم التحديث بنجاح')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    }
}

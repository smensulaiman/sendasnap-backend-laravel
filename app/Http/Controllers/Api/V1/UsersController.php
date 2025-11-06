<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API endpoints for user data fetch management"
 * )
 */
class UsersController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="List users",
     *     description="Get a list of all users",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="users",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Mohammad Sulaiman"),
     *                         @OA\Property(property="email", type="string", format="email", example="sulaiman@sendasnap.com"),
     *                         @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
     *                         @OA\Property(property="role", type="string", example="employee"),
     *                         @OA\Property(property="avis_id", type="string", example="12312"),
     *                         @OA\Property(property="avatar", type="string", example="photo url"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T12:34:56Z")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="timestamp", type="string", format="date-time", example="2025-01-01T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="errors", type="object", example={}),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="timestamp", type="string", format="date-time", example="2025-01-01T12:34:56Z")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'phone', 'role', 'avis_id', 'avatar', 'created_at'])
            ->get();

        return $this->successResponse('Users retrieved successfully', [
            'users' => $users,
        ]);
    }
}

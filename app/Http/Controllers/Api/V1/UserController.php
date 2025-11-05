<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API endpoints for user management"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="List users",
     *     description="Get a paginated list of users",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="search", in="query", description="Search query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="role", in="query", description="Filter by role", required=false, @OA\Schema(type="string", enum={"admin","manager","employee","client"})),
     *
     *     @OA\Response(response=200, description="Users retrieved successfully")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->get('role'));
        }

        $users = $query->paginate($request->get('per_page', 15));

        return $this->successResponse('Users retrieved successfully', [
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Create user",
     *     description="Create a new user (Admin only)",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation","role"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"admin","manager","employee","client"}, example="employee"),
     *             @OA\Property(property="phone", type="string", example="+1234567890")
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="User created successfully")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,employee,client',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            $messages = implode(' ', $validator->errors()->all());

            return $this->errorResponse($messages ?: 'Validation failed', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        return $this->successResponse('User created successfully', [
            'user' => $user,
        ], 201);
    }

    /**
     * Managers can create new employees quickly.
     */
    public function storeEmployee(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            $messages = implode(' ', $validator->errors()->all());

            return $this->errorResponse($messages ?: 'Validation failed', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'phone' => $request->phone,
        ]);

        return $this->successResponse('Employee created successfully', [
            'user' => $user,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Get user",
     *     description="Get a specific user by ID",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", description="User ID", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="User retrieved successfully")
     * )
     */
    public function show(User $user): JsonResponse
    {
        return $this->successResponse('User retrieved successfully', [
            'user' => $user,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     summary="Update user",
     *     description="Update an existing user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", description="User ID", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=false,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="role", type="string", enum={"admin","manager","employee","client"})
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="User updated successfully")
     * )
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|in:admin,manager,employee,client',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            $messages = implode(' ', $validator->errors()->all());

            return $this->errorResponse($messages ?: 'Validation failed', $validator->errors(), 422);
        }

        $updateData = $request->only(['name', 'email', 'role', 'phone']);

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return $this->successResponse('User updated successfully', [
            'user' => $user,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     summary="Delete user",
     *     description="Delete a user (Admin only)",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", description="User ID", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="User deleted successfully")
     * )
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deletion of the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return $this->errorResponse('Cannot delete the last admin user', [], 400);
        }

        $user->delete();

        return $this->successResponse('User deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/{id}/assign-role",
     *     summary="Assign role to user",
     *     description="Assign a role to a user (Admin only)",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", description="User ID", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"role"},
     *
     *             @OA\Property(property="role", type="string", enum={"admin","manager","employee","client"}, example="manager")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Role assigned successfully")
     * )
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,manager,employee,client',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }

        $user->update(['role' => $request->role]);

        return $this->successResponse('Role assigned successfully', [
            'user' => $user,
        ]);
    }

    /**
     * Success response helper
     */
    protected function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }

    /**
     * Error response helper
     */
    protected function errorResponse(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }
}

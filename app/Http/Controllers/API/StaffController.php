<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function addStaff(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|string|max:200',
                'roles.*' => 'in:dokter,cashier,product_manager',
            ]);

            $userOwner = Auth::user();
            if (!$userOwner->hasRole('petshop_owner')) {
                return response()->json([
                    'message' => 'Only petshop owners are allowed to add staff.',
                ], 403);
            }
    
            $user = User::where('email', $request->email)->firstOrFail();
    
            $staff = Staff::where('id', $user->id)->first();
    
            if ($staff) {
                // If staff already exists, retrieve the user's existing roles and add the new roles
                $existingRoles = $user->roles->pluck('name')->toArray();
                $newRoles = array_unique(array_merge($existingRoles, $request->roles));
    
                // Check if any of the new roles already exist for the user
                $duplicateRoles = array_intersect($existingRoles, $request->roles);
                if (!empty($duplicateRoles)) {
                    return response()->json([
                        'message' => 'Cannot add staff. The user already has the following role(s): ' . implode(', ', $duplicateRoles),
                    ], 400);
                }
    
                $user->syncRoles($newRoles);
            } else {
                // If staff does not exist, create a new record
                $user->assignRole('petshop_staff');
    
                $staffCount = Staff::where('petshop_id', $request->petshop_id)->count();
    
                if ($staffCount >= 6) {
                    return response()->json([
                        'message' => 'Cannot add staff. The petshop already has the maximum number of staff.',
                    ], 400);
                }
                
                
                Staff::create([
                    'user_id' => $user->id,
                    'petshop_id' => $userOwner->petshop_id,
                ]);

                $user->petshop_id = $userOwner->petshop_id;
                $user->save();
    
                if ($request->has('roles')) {
                    foreach ($request->roles as $role) {
                        if ($user->hasRole($role)) {
                            return response()->json([
                                'message' => 'Cannot add role. The user already has the ' . $role . ' role.',
                            ], 400);
                        }
                        $user->assignRole($role);
                    }
                }
            }
            return response()->json([
                'message' => 'Staff added/updated successfully.',
                'data' => $user,
            ]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getPetshopDoctors(Request $request, $petshopId)
    {
        try {
            $doctors = Staff::where('petshop_id', $petshopId)
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'dokter');
            })
            // ->whereHas('jam_operasional')
            ->with('user')
            ->get();
            
            // dd($doctors);
            return response()->json($doctors);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }


    public function getPetshopStaffs(Request $request, $petshopId)
    {
        try {
            // Ensure the user is a petshop owner
            // $petshopId = Auth::user()->petshop_id;

            // Get the list of doctors for the petshop
            $staffs = User::with(['roles:name'])->where('petshop_id', $petshopId)->role('petshop_staff')->get();
            $response = [];
            foreach($staffs as $staff) {
                array_push($response, [
                'doctor' => $staff,
                'links' => '/doctor/edit-doctor/' . $staff->id, 
                ]);
            }
            return response()->json([
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);    
        }
    }

    public function removeRole(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|string|max:200',
                'roles' => 'required|array',
            ]);
        
            $user = User::where('email', $request->email)->firstOrFail();
        
            foreach ($request->roles as $roleName) {
                if (!$user->hasRole($roleName)) {
                    return response()->json([
                        'message' => 'Cannot remove role. The user does not have the ' . $roleName . ' role.',
                    ], 400);
                }
        
                $role = Role::where('name', $roleName)->first();
                $user->removeRole($role);
            }
        
            return response()->json([
                'message' => 'Role(s) removed successfully.',
                'data' => $user,
            ]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    
    public function removePetshopStaff(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
        
            $user = User::findOrFail($request->user_id);
        
            if (!$user->hasRole('petshop_staff')) {
                return response()->json([
                    'message' => 'Cannot remove petshop staff role. The user does not have the petshop_staff role.',
                ], 400);
            }
        
            // Get all roles except customer
            $roles = $user->roles->where('name', '<>', 'customer');
        
            // Remove all roles except customer
            foreach ($roles as $role) {
                $user->removeRole($role);
            }
        
            $user->removeRole('petshop_staff');
        
            $staff = Staff::where('user_id', $user->id)->first();
        
            if ($staff) {
                $staff->delete();
            }

            $user->petshop_id = null;
            $user->save();
        
            return response()->json([
                'message' => 'Petshop staff role removed successfully.',
                'data' => $user,
            ]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}

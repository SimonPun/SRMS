<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('backend.auth.admin-login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('backend.auth.register');
    }

    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('backend.auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->withErrors([
                'email' => __($status),
            ])->onlyInput('email');
        }

        return back()->with('success', 'Password reset link sent. Please check your email.');
    }

    public function showResetPasswordForm(string $token, Request $request)
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('backend.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => __($status),
            ])->withInput($request->only('email'));
        }

        return redirect()->route('login')->with('success', 'Password has been reset successfully.');
    }

    // Login function
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors([
                'email' => 'Invalid credentials'
            ])->onlyInput('email');
        }

        // Regenerate the session on login to prevent session fixation.
        $request->session()->regenerate();
        return $this->redirectByRole(Auth::user())
            ->with('loginSuccess', 'Logged in successfully.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'client',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard')
            ->with('loginSuccess', 'Account created successfully.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Logged out successfully');
    }

    // Admin Dashboard
    public function dashboard()
    {
        $user = Auth::user();
        $totalRequests = ServiceRequest::count();
        $pendingRequests = ServiceRequest::where('status', 'pending')->count();
        $inProgressRequests = ServiceRequest::where('status', 'in_progress')->count();
        $completedRequests = ServiceRequest::where('status', 'completed')->count();
        $totalStaff = User::where('role', 'service_staff')->count();
        $totalClients = User::whereIn('role', ['client', 'user', 'requester'])->count();

        return view('backend.dashboard.admin_dashboard', compact(
            'user',
            'totalRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'totalStaff',
            'totalClients'
        ));
    }

    public function categories()
    {
        $categories = ServiceCategory::orderBy('name')->get();

        return view('backend.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validateWithBag('createCategory', [
            'name' => 'required|string|max:100|unique:service_categories,name',
        ]);

        ServiceCategory::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    public function updateCategory(Request $request, ServiceCategory $category)
    {
        $validated = $request->validateWithBag('updateCategory', [
            'update_name' => 'required|string|max:100|unique:service_categories,name,' . $category->id,
        ]);

        $oldName = $category->name;
        $newName = $validated['update_name'];

        DB::transaction(function () use ($category, $oldName, $newName) {
            $category->update([
                'name' => $newName,
            ]);

            ServiceRequest::where('category', $oldName)->update([
                'category' => $newName,
            ]);
        });

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(ServiceCategory $category)
    {
        $isUsed = ServiceRequest::where('category', $category->name)->exists();

        if ($isUsed) {
            return back()->with('error', 'Cannot delete this category because it is already used in requests.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    // Manage Users
    public function manageUsers()
    {
        $users = User::orderBy('name')->get();

        return view('backend.users.index', compact('users'));
    }

    public function createStaffUser()
    {
        return view('backend.users.create_staff');
    }

    public function storeStaffUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'service_staff',
        ]);

        return back()->with('success', 'Service staff account created successfully.');
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:service_staff,client',
        ]);

        if ($user->role === 'admin') {
            return back()->with('error', 'Admin role cannot be changed from this page.');
        }

        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', 'User role updated successfully.');
    }

    // Show Profile
    public function showProfile()
    {
        $user = Auth::user();

        return view('backend.auth.admin-setting', [
            'user' => $user,
            'updateRoute' => route('profile.update'),
        ]);
    }

    // Update Profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    protected function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'service_staff' => redirect()->route('staff.dashboard'),
            'user', 'requester', 'client' => redirect()->route('user.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }
}

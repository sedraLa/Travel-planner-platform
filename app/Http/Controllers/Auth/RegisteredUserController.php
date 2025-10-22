<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Enums\UserRole;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;
use App\Models\Driver;
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function store(Request $request): RedirectResponse
{
    $role = $request->input('role', UserRole::USER->value);

    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'last_name' => ['required', 'string', 'max:255'],
        'phone_number' => ['required', 'string', 'max:255'],
        'country' => ['required', 'string', 'max:255'],
    ];

    if ($role === UserRole::DRIVER->value) {
        $rules = array_merge($rules, [
            'license_category' => 'nullable|string|max:50',
            'license_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'experience' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:18|max:100',
        ]);
    }

    $validated = $request->validate($rules);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'last_name' => $request->last_name,
        'phone_number' => $request->phone_number,
        'country' => $request->country,
        'role' => $role,
    ]);

    if ($role === UserRole::DRIVER->value) {
        $licensePath = null;

        if ($request->hasFile('license_image')) {
            $licensePath = MediaServices::save(
                $request->file('license_image'),
                'image',
                'drivers'
            );
        }

        Driver::create([
            'user_id' => $user->id,
            'age' => $request->age,
            'address' => $request->address,
            'license_image' => $licensePath,
            'license_category' => $request->license_category,
            'experience' => $request->experience,
            'status' => 'pending',
            'date_of_hire' => null,
        ]);

        return redirect()->route('login')
            ->with('success', 'تم إرسال طلبك للمراجعة. سيتم إعلامك بعد الموافقة.');
    }

    event(new Registered($user));
    Auth::login($user);

    return redirect(RouteServiceProvider::HOME);
}

}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Enums\UserRole;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;
use App\Models\Driver;
use App\Models\Guide;

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
    public function store(RegisterRequest $request): RedirectResponse
{
    $validated = $request->validated();

    $role = $validated['role'];

    // create user
    $user = User::create([

        'name' => $validated['name'],

        'last_name' => $validated['last_name'],

        'email' => $validated['email'],

        'password' => Hash::make($validated['password']),

        'phone_number' => $validated['phone_number'],

        'country' => $validated['country'],

        'role' => $role,

    ]);

    // if driver
    if ($role === UserRole::DRIVER->value) {

        $licensePath = MediaServices::save(
            $request->file('license_image'),
            'images',
            'drivers'
        );

        $personalPath = MediaServices::save(
            $request->file('personal_image'),
            'images',
            'drivers'
        );

        Driver::create([

            'user_id' => $user->id,

            'age' => $validated['age'] ?? null,

            'address' => $validated['address'] ?? null,

            'license_image' => $licensePath,

            'personal_image' => $personalPath,

            'license_category' => $validated['license_category'],

            'experience' => $validated['experience'] ?? null,

            'status' => 'pending',

            'date_of_hire' => null,

        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Driver request sent successfully');
    }


      if ($role === UserRole::GUIDE->value) {

        $certificatePath = null;

        if ($request->hasFile('certificate_image')) {
            $certificatePath = MediaServices::save(
                $request->file('certificate_image'),
                'images',
                'guides'
            );
        }


         $personalPath = MediaServices::save(
            $request->file('personal_image'),
            'images',
            'guides'
        );

        Guide::create([
            'user_id' => $user->id,

            'bio' => $validated['bio'] ?? null,

            'languages' => $validated['languages'] ?? null,

            'years_of_experience' => $validated['years_of_experience'] ?? null,

            'certificate_image' => $certificatePath,

            'status' => 'pending',

            'personal_image' => $personalPath,

            'age' => $validated['age'] ?? null,

            'address' => $validated['address'] ?? null,

            'date_of_hire' => null,

            'is_tour_leader' => $request->has('is_tour_leader'),
          
            
        ]);

          if ($request->filled('specializations')) {
             $guide->specializations()->sync($request->specializations);
        }
    }

    // normal user
    event(new Registered($user));

    Auth::login($user);

    return redirect(RouteServiceProvider::HOME);
}

}

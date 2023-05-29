<?php

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Country;
use App\Models\CustomerAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function view(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;
        $shoppingAddress = $customer->shippingAddress ?: new CustomerAddress(['type' => AddressType::Shipping]);
        $billingAddress = $customer->billingAddress ?: new CustomerAddress(['type' => AddressType::Billing]);
        $countries = Country::query()->orderBy('name')->get();

        return view('profile.view', compact('customer', 'user', 'shippingAddress', 'billingAddress', 'countries'));
    }

    public function store(ProfileRequest $request)
    {
        try {
            DB::beginTransaction();

            $customerData = $request->validated();
            $shippingData = $customerData['shipping'];
            $billingData = $customerData['billing'];

            /** @var \App\Models\User $user */
            $user = $request->user();
            /** @var \App\Models\Customer $customer */
            $customer = $user->customer;

            $customer->update($customerData);

            if ($customer->shippingAdress) {
                $customer->shippingAddress->update($shippingData);
            } else {
                $shippingData['customer_id'] = $customer->user_id;
                $shippingData['type'] = AddressType::Shipping->value;
                CustomerAddress::create($shippingData);
            }

            if ($customer->billingAdress) {
                $customer->billingAddress->update($billingData);
            } else {
                $billingData['customer_id'] = $customer->user_id;
                $billingData['type'] = AddressType::Billing->value;
                CustomerAddress::create($billingData);
            }

            DB::commit();

            $request->session()->flash('flash_message', 'Profile was successfully updated.');

            return redirect()->route('profile');
        } catch (Throwable $e) {
            DB::rollBack();

            // throw $e;

            $request->session()->flash('flash_message', 'An error occured. Please try again.');

            return redirect()->route('profile');
        }
    }

    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $passwordData = $request->validated();

            $user->password = Hash::make($passwordData['new_password']);
            $user->save();


            $request->session()->flash('flash_message', 'Your password was successfully updated');


            return redirect()->route('profile');
        } catch (Throwable $e) {
            // throw $e;

            $request->session()->flash('flash_message', 'An error occured. Please try again.');

            return redirect()->route('profile');
        }
    }
}

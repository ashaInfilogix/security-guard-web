<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\GuardAdditionalInformation;
use App\Models\ContactDetail;
use App\Models\UsersBankDetail;
use App\Models\UsersKinDetail;
use App\Models\UsersDocuments;
use Carbon\Carbon;

class ProfileController extends Controller
{
    private function parseDate($date)
    {
        if (empty($date)) {
            return null; 
        }

        try {
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function editProfile(Request $request)
    {
        $guardInfo = GuardAdditionalInformation::where('user_id', Auth::id())->first();
        $usersBankDetail = UsersBankDetail::where('user_id', Auth::id())->first();
        $validator = Validator::make($request->all(), [
            'first_name'        => 'required',
            'email'             => 'nullable|email|unique:users,email,' . Auth::id(),
            'phone_number'      => 'nullable|numeric|unique:users,phone_number,' . Auth::id(),
            'trn_doc'           => 'nullable',
            'nis_doc'           => 'nullable',
            'psra_doc'          => 'nullable',
            'birth_certificate' => 'nullable',
            'trn'               => 'nullable|unique:guard_additional_information,trn,'. optional($guardInfo)->id,
            'nis'               => 'nullable|unique:guard_additional_information,nis,'. optional($guardInfo)->id,
            'psra'              => 'nullable|unique:guard_additional_information,psra,'. optional($guardInfo)->id,
            'account_no'        => 'nullable|unique:users_bank_details,account_no,'. optional($usersBankDetail)->id,
            'date_of_joining'   => 'nullable|date_format:d-m-Y',
            'date_of_birth'     => 'required|date|before:date_of_joining|date_format:d-m-Y',
            'date_of_separation'=> 'nullable|date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'  => false,
                'message'  => $validator->errors()->first()
            ]);
        }

        $user = User::where('id', Auth::id())->first();
        $user->first_name   = $request->first_name;
        $user->surname      = $request->surname;
        $user->middle_name  = $request->middle_name;
        $user->email        = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($user->profile_picture) {
            $oldProfilePicturePath = public_path($user->profile_picture);
            if (file_exists($oldProfilePicturePath)) {
                unlink($oldProfilePicturePath);
            }
        }
        if ($request->hasFile('profile_picture')) {
            $user->profile_picture = uploadFile($request->file('profile_picture'), 'uploads/profile-pic/');
        }
        $user->save();

        // Update related records
        if ($guardInfo) {
            $guardInfo->update([
                'trn'                   => $request->trn,
                'nis'                   => $request->nis,
                'psra'                  => $request->psra,
                'date_of_joining'       => $this->parseDate($request->date_of_joining),
                'date_of_birth'         => $this->parseDate($request->date_of_birth),
                'guard_type_id'         => $request->guard_type_id,
                'guard_employee_as_id'  => $request->guard_employee_as_id,
                'date_of_seperation'    => $this->parseDate($request->date_of_seperation),
            ]);
        }

        $contactDetail = ContactDetail::where('user_id', Auth::id())->first();
        if ($contactDetail) {
            $contactDetail->update([
                'apartment_no'  => $request->apartment_no,
                'building_name' => $request->building_name,
                'street_name'   => $request->street_name,
                'parish'        => $request->parish,
                'city'          => $request->city,
                'postal_code'   => $request->postal_code,
            ]);
        }

        UsersBankDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'bank_name'           => $request->bank_name,
                'bank_branch_address' => $request->branch,
                'account_no'          => $request->account_number,
                'account_type'        => $request->account_type,
                'routing_number'      => $request->routing_number,
                'recipient_id'        => $request->recipient_id
            ]
        );

        UsersKinDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'surname'        => $request->kin_surname,
                'first_name'     => $request->kin_first_name,
                'middle_name'    => $request->kin_middle_name,
                'apartment_no'   => $request->kin_apartment_no,
                'building_name'  => $request->kin_building_name,
                'street_name'    => $request->kin_street_name,
                'parish'         => $request->kin_parish,
                'city'           => $request->kin_city,
                'postal_code'    => $request->kin_postal_code,
                'email'          => $request->kin_email,
                'phone_number'   => $request->kin_phone_number,
            ]
        );

        $documents = [];
        if ($request->hasFile('trn_doc')) {
            $documents['trn'] = uploadFile($request->file('trn_doc'), 'uploads/user-documents/trn/');
        }
        if ($request->hasFile('nis_doc')) {
            $documents['nis'] = uploadFile($request->file('nis_doc'), 'uploads/user-documents/nis/');
        }
        if ($request->hasFile('psra_doc')) {
            $documents['psra'] = uploadFile($request->file('psra_doc'), 'uploads/user-documents/psra/');
        }
        if ($request->hasFile('birth_certificate')) {
            $documents['birth_certificate'] = uploadFile($request->file('birth_certificate'), 'uploads/user-documents/birth_certificate/');
        }

        usersDocuments::updateOrCreate(
            ['user_id' => Auth::id()],
            $documents
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => $user
        ]);
    }

    public function guardProfile()
    {
        $guard = User::with([
                'guardAdditionalInformation', 'contactDetail', 'usersBankDetail','usersKinDetail','userDocuments'
            ])->where('id',Auth::id())->first();

        if($guard){
            return response()->json([
                'success' => true,
                'data'    => $guard
            ]); 
        }else{
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function changeProfileImage(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required'
        ]);

        $user = User::where('id', Auth::id())->first();
        if ($user->profile_picture) {
            $oldProfilePicturePath = public_path($user->profile_picture);
            if (file_exists($oldProfilePicturePath)) {
                unlink($oldProfilePicturePath);
            }
        }
        $user->profile_picture = uploadFile($request->file('profile_picture'), 'uploads/profile-pic/');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile picture updated successfully.',
            'data'    => $user
        ]);
    }
}

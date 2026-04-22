<?php

use App\Http\Controllers\TeacherAssessmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Routing\Route;

Route::post('/login', function (Request $request) {

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $request->password)) {
        return response()->json([
            'message' => 'Login gagal'
        ], 401);
    }

    $token = $user->createToken('postman')->plainTextToken;

    return response()->json([
        'token' => $token
    ]);
});

Route::middleware('auth:sanctum')->post(
    '/homeroom/assessments',
    [TeacherAssessmentController::class, 'store']
);
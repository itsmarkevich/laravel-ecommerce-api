<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CompleteRegistrationRequest;
use App\Http\Requests\Auth\SendCodeRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\PhoneVerificationService;
use App\Services\Auth\RegistrationService;
use App\Services\Auth\TokenService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{
    public function __construct(
        private RegistrationService      $registrationService,
        private PhoneVerificationService $phoneVerificationService,
        private TokenService             $tokenService,
    ) {}

    public function sendVerificationCode(SendCodeRequest $request): JsonResponse
    {
        $this->phoneVerificationService->sendCode($request->validated()['phone']);
        return response()->json([
            'success' => true,
            'message' => 'Код отправлен',
        ]);
    }

    public function verifyAndLogin(VerifyCodeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $isCodeValid = $this->phoneVerificationService->verifyCode(
            $validated['phone'],
            $validated['code']
        );

        if (!$isCodeValid) {
            throw new AuthenticationException('Неверный код');
        }

        $user = $this->registrationService->findOrCreateByPhone($validated['phone']);

        $this->registrationService->markPhoneVerified($user);

        $tokenData = $this->tokenService->login($user);

        $requiresProfileCompletion = !$user->name || !$user->email;

        return response()->json(array_merge([
            'user' => new UserResource($user),
            'requires_profile_completion' => $requiresProfileCompletion,
        ], $tokenData));
    }

    public function completeUserProfile(CompleteRegistrationRequest $request): JsonResponse
    {
        /**
         * @var User $user
         * */

        $user = auth()->user();
        $validated = $request->validated();

        $updatedUser = $this->registrationService->completeProfile(
            $user,
            $validated['name'],
            $validated['email'],
        );

        return response()->json([
            'user' => new UserResource($updatedUser),
            'message' => 'Профиль успешно заполнен',
        ]);
    }

    public function refreshAccessToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            throw new AuthenticationException('Token not provided');
        }

        try {
            $tokenData = $this->tokenService->refresh($token);
            return response()->json($tokenData);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token cannot be refreshed'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['error' => 'Token blacklisted'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }

    public function userLogout(): JsonResponse
    {
        $this->tokenService->logout();
        return response()->json(null, 204);
    }
}

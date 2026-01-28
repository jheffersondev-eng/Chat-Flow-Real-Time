<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Chat Application API",
 *     version="1.0.0",
 *     description="Real-time chat application with OAuth2, 2FA, and LLM bot integration"
 * )
 * @OA\Server(
 *     url="http://localhost",
 *     description="Local Development Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}

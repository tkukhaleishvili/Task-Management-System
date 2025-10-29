<?php
use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Controllers\PasswordResetController;

return [
    'GET' => [
        '/'                 => [AuthController::class, 'dashboard'],
        '/login'            => [AuthController::class, 'showLogin'],
        '/register'         => [AuthController::class, 'showRegister'],
        '/password/forgot'  => [PasswordResetController::class, 'showRequest'],
        '/password/reset'   => [PasswordResetController::class, 'showReset'],

        '/tasks'            => [TaskController::class, 'index'],  
        '/tasks/create'     => [TaskController::class, 'create'],  
        '/task'             => [TaskController::class, 'show'],   
        '/tasks/edit'       => [TaskController::class, 'edit'],    
    ],

    'POST' => [
        '/login'            => [AuthController::class, 'login'],
        '/logout'           => [AuthController::class, 'logout'],
        '/register'         => [AuthController::class, 'register'],
        '/password/forgot'  => [PasswordResetController::class, 'request'],
        '/password/reset'   => [PasswordResetController::class, 'reset'],

        '/tasks/store'      => [TaskController::class, 'store'],
        '/tasks/update'     => [TaskController::class, 'update'],
        '/tasks/delete'     => [TaskController::class, 'destroy'],
        '/tasks/status'     => [TaskController::class, 'changeStatus'], 
        '/tasks/comment'    => [TaskController::class, 'addComment'],
        '/tasks/attach'     => [TaskController::class, 'attachFile'],
    ],
];


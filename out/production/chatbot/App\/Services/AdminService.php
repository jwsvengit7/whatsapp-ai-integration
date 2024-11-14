<?php

namespace App\Services;

use Illuminate\Http\Request;

interface AdminService
{
    public function getContext(): \Illuminate\Http\JsonResponse;
    public function createProduct(\Illuminate\Http\Request $request);
    public function createContext(Request $request): \Illuminate\Http\JsonResponse;
    public function createAdmin(\Illuminate\Http\Request $request);
    public function fetchAllUsers();
    public function fetchConversation(\Illuminate\Http\Request $request);
    public function createScheduledMessage(Request $request): \Illuminate\Http\JsonResponse;
    public function generateCalendarGif();
}

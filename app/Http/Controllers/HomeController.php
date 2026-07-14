<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Home\HomeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(protected HomeService $homeService) {}

    public function index(): View
    {
        $user = Auth::user();
        assert($user instanceof User);

        return view(
            'home.index',
            $this->homeService->getHomeData($user)
        );
    }
}

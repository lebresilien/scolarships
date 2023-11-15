<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\{ User, Section, Building, Group, Classroom, Account };
use App\Repositories\DashboardRepository;
use App\Repositories\AccountRepository;
use App\Traits\ApiResponser;
use App\Services\Service;

class DashboardController extends Controller
{
    use ApiResponser;
    /** @var DashboardRepository */
    private $dashboardRepository;
    private $service;

    public function __construct(Service $service, DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
        $this->service = $service;
    }
    
    public function statistics(Request $request) {

        $data = $this->dashboardRepository->statistics($request->user()->accounts[0]->id, $this->service->currentAcademy($request)->id);

        return $this->success($data, 'Statistics');
    }
}

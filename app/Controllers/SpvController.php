<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class SpvController extends BaseController
{
    public function spv()
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->where('role', 'spv')->findAll();
        return view('page/user/spv');
    }

    public function json() {
        $request = service('request');
        $model = new UserModel();

        $searchValue = $request->getGet('search')['value'] ?? '';
        $start = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');
        $draw = (int) $request->getGet('draw');

        $query  = $model->where('role', 'spv');

        if($searchValue) {
            $query = $query
            ->groupStart()
            ->like('nama', $searchValue)
            ->orLike('username', $searchValue)
            ->orLike('status', $searchValue)
            ->groupEnd();
        }

        $query = $query->orderBy('created_at', 'DESC');
        $filteredQuery = clone $query;

        $data = $query->findAll($length, $start);
        $total = $model->where('role', 'spv')->countAllResults();
        $filtered = $searchValue ? $filteredQuery->countAllResults(false) : $total;

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ]);

    }
}

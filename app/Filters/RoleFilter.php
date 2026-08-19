<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');
        $allowedRoles = $arguments ?? [];

        if (!$allowedRoles || in_array($role, $allowedRoles, true)) {
            return null;
        }

        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Kamu tidak punya akses ke fitur ini.',
                ]);
        }

        return redirect()->to('/dashboard')->with('error', 'Kamu tidak punya akses ke fitur ini.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\ApiTokens;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class ApiTokenController extends Controller
{
    private ApiTokenRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new ApiTokenRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);

        $result = $this->repo->findByOrganization($orgId, $page, $perPage);

        // Mask token hashes
        foreach ($result['data'] as &$token) {
            unset($token['token_hash']);
        }

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $token = $this->repo->findById($id);
        if (!$token) {
            throw new NotFoundException('API token not found');
        }
        unset($token['token_hash']);
        return $this->success($token);
    }

    /**
     * POST /api/api-tokens — Generate a new API token
     */
    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name' => 'required|string|max:255',
            'abilities' => 'nullable|json',
            'expires_at' => 'nullable|datetime',
        ]);

        $orgId = $request->organizationId();
        $plainToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);

        $id = $this->repo->create([
            'organization_id' => $orgId,
            'user_id' => $request->userId(),
            'name' => Sanitizer::string($data['name']),
            'token_hash' => $tokenHash,
            'abilities' => is_array($data['abilities'] ?? null) ? json_encode($data['abilities']) : ($data['abilities'] ?? json_encode(['*'])),
            'expires_at' => $data['expires_at'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $token = $this->repo->findById($id);
        unset($token['token_hash']);

        // Return the plain token only once
        $token['plain_token'] = $plainToken;

        return $this->created($token, 'API token created. Store the token securely — it will not be shown again.');
    }

    public function revoke(Request $request, int $id): Response
    {
        $token = $this->repo->findById($id);
        if (!$token) {
            throw new NotFoundException('API token not found');
        }

        $this->repo->revokeToken($id);
        return $this->success(null, 'API token revoked');
    }

    public function revokeAll(Request $request): Response
    {
        $this->repo->revokeAllForOrganization($request->organizationId());
        return $this->success(null, 'All API tokens revoked');
    }
}

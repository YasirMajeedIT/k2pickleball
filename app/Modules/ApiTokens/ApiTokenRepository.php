<?php

declare(strict_types=1);

namespace App\Modules\ApiTokens;

use App\Core\Database\Repository;

final class ApiTokenRepository extends Repository
{
    protected string $table = 'api_tokens';

    public function create(array $data): int
    {
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        // api_tokens table has no updated_at column
        unset($data['updated_at']);
        return $this->db->insert($this->table, $data);
    }

    public function findByOrganization(int $orgId, int $page = 1, int $perPage = 20): array
    {
        return $this->query()
            ->where('organization_id', $orgId)
            ->orderBy('created_at', 'DESC')
            ->paginate($page, $perPage);
    }

    public function findByToken(string $tokenHash): ?array
    {
        return $this->query()
            ->where('token_hash', $tokenHash)
            ->first();
    }

    public function recordLastUsed(int $id): void
    {
        $this->db->update('api_tokens', [
            'last_used_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    public function revokeToken(int $id): void
    {
        $this->db->update('api_tokens', [
            'revoked_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    public function revokeAllForOrganization(int $orgId): void
    {
        $this->db->query(
            "UPDATE `api_tokens` SET `revoked_at` = NOW() WHERE `organization_id` = ? AND `revoked_at` IS NULL",
            [$orgId]
        );
    }
}

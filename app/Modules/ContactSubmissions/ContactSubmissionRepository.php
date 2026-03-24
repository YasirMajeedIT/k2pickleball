<?php

declare(strict_types=1);

namespace App\Modules\ContactSubmissions;

use App\Core\Database\Connection;

final class ContactSubmissionRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create(array $data): int
    {
        $data['uuid'] = $this->generateUuid();
        return $this->db->insert('contact_submissions', $data);
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM contact_submissions WHERE id = ?', [$id]) ?: null;
    }

    public function findAllPaginated(?string $search, ?string $status, ?string $subject, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = [];
        $params = [];

        if ($search) {
            $like = "%{$search}%";
            $where[]  = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR message LIKE ?)';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        if ($status) {
            $where[]  = 'status = ?';
            $params[] = $status;
        }

        if ($subject) {
            $where[]  = 'subject = ?';
            $params[] = $subject;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = $this->db->fetch(
            "SELECT COUNT(*) AS cnt FROM contact_submissions {$whereClause}",
            $params
        );

        $data = $this->db->fetchAll(
            "SELECT * FROM contact_submissions {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return ['data' => $data, 'total' => (int) ($total['cnt'] ?? 0)];
    }

    public function updateStatus(int $id, string $status, ?string $notes = null): void
    {
        $data = ['status' => $status];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        $this->db->update('contact_submissions', $data, ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->db->delete('contact_submissions', ['id' => $id]);
    }

    public function stats(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT status, COUNT(*) AS cnt FROM contact_submissions GROUP BY status'
        );
        $stats = ['new' => 0, 'read' => 0, 'replied' => 0, 'archived' => 0, 'total' => 0];
        foreach ($rows as $r) {
            $stats[$r['status']] = (int) $r['cnt'];
            $stats['total'] += (int) $r['cnt'];
        }
        return $stats;
    }

    private function generateUuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}

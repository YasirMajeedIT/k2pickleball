<?php

declare(strict_types=1);

namespace App\Core\Database;

use App\Core\Exceptions\NotFoundException;

/**
 * Abstract tenant-aware repository.
 * All module repositories extend this class for automatic tenant scoping.
 */
abstract class Repository
{
    protected Connection $db;
    protected string $table;
    protected ?int $organizationId = null;
    protected string $primaryKey = 'id';

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Set the organization scope for all queries.
     */
    public function setOrganizationId(?int $organizationId): self
    {
        $this->organizationId = $organizationId;
        return $this;
    }

    /**
     * Create a new query builder scoped to this repository's table and tenant.
     */
    protected function query(): QueryBuilder
    {
        $builder = new QueryBuilder($this->db);
        $builder->setTable($this->table);

        if ($this->organizationId !== null) {
            $builder->forTenant($this->organizationId);
        }

        return $builder;
    }

    /**
     * Find a record by its primary key.
     */
    public function findById(int $id): ?array
    {
        return $this->query()->where($this->primaryKey, $id)->first();
    }

    /**
     * Find a record by ID or throw NotFoundException.
     */
    public function findOrFail(int $id): array
    {
        $record = $this->findById($id);

        if ($record === null) {
            throw new NotFoundException(
                ucfirst(rtrim($this->table, 's')) . ' not found'
            );
        }

        return $record;
    }

    /**
     * Find all records with optional pagination.
     */
    public function findAll(int $page = 1, int $perPage = 20, string $orderBy = 'created_at', string $direction = 'DESC'): array
    {
        return $this->query()
            ->orderBy($orderBy, $direction)
            ->paginate($page, $perPage);
    }

    /**
     * Find records matching conditions.
     */
    public function findWhere(array $conditions, int $page = 1, int $perPage = 20): array
    {
        $query = $this->query();

        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        return $query->orderBy('created_at', 'DESC')->paginate($page, $perPage);
    }

    /**
     * Find a single record matching conditions.
     */
    public function findOneWhere(array $conditions): ?array
    {
        $query = $this->query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->first();
    }

    /**
     * Create a new record.
     */
    public function create(array $data): int
    {
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        $data['updated_at'] = $data['updated_at'] ?? date('Y-m-d H:i:s');

        if ($this->organizationId !== null && !isset($data['organization_id'])) {
            $data['organization_id'] = $this->organizationId;
        }

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update a record by ID.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $where = [$this->primaryKey => $id];

        if ($this->organizationId !== null) {
            $where['organization_id'] = $this->organizationId;
        }

        return $this->db->update($this->table, $data, $where) > 0;
    }

    /**
     * Delete a record by ID (hard delete by default).
     */
    public function delete(int $id, bool $soft = false): bool
    {
        if ($soft) {
            return $this->update($id, ['status' => 'deleted']);
        }

        $where = [$this->primaryKey => $id];
        if ($this->organizationId !== null) {
            $where['organization_id'] = $this->organizationId;
        }

        return $this->db->delete($this->table, $where) > 0;
    }

    /**
     * Count total records.
     */
    public function count(array $conditions = []): int
    {
        $query = $this->query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->count();
    }

    /**
     * Check if a record exists.
     */
    public function exists(array $conditions): bool
    {
        $query = $this->query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->exists();
    }

    /**
     * Search records by a keyword across specified columns.
     */
    public function search(string $keyword, array $columns, int $page = 1, int $perPage = 20): array
    {
        $query = $this->query();

        if (!empty($keyword) && !empty($columns)) {
            $searchValue = "%{$keyword}%";
            $conditions = [];
            $bindings = [];
            foreach ($columns as $col) {
                $conditions[] = "`{$col}` LIKE ?";
                $bindings[] = $searchValue;
            }
            $query->whereRaw('(' . implode(' OR ', $conditions) . ')', $bindings);
        }

        return $query->orderBy('created_at', 'DESC')->paginate($page, $perPage);
    }
}

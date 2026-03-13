<?php

declare(strict_types=1);

namespace App\Core\Database;

/**
 * Fluent query builder with automatic tenant scoping.
 * All queries are executed with prepared statements.
 */
final class QueryBuilder
{
    private Connection $db;
    private string $table = '';
    private array $select = ['*'];
    private array $where = [];
    private array $params = [];
    private array $joins = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private ?int $tenantId = null;
    private string $tenantColumn = 'organization_id';
    private array $groupBy = [];
    private ?string $having = null;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public static function table(string $table): self
    {
        $instance = new self(Connection::getInstance());
        $instance->table = $table;
        return $instance;
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Scope all queries to a specific tenant.
     */
    public function forTenant(int $tenantId, string $column = 'organization_id'): self
    {
        $this->tenantId = $tenantId;
        $this->tenantColumn = $column;
        return $this;
    }

    public function select(string ...$columns): self
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, mixed $operator, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];
        $this->params[] = $value;

        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            // Force no results for empty IN clause
            $this->where[] = ['type' => 'raw', 'sql' => '1 = 0'];
            return $this;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->where[] = [
            'type' => 'raw',
            'sql' => "`{$column}` IN ({$placeholders})",
        ];
        $this->params = array_merge($this->params, array_values($values));

        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->where[] = ['type' => 'raw', 'sql' => "`{$column}` IS NULL"];
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->where[] = ['type' => 'raw', 'sql' => "`{$column}` IS NOT NULL"];
        return $this;
    }

    public function whereLike(string $column, string $value): self
    {
        $this->where[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => 'LIKE',
            'value' => $value,
        ];
        $this->params[] = $value;

        return $this;
    }

    public function whereBetween(string $column, mixed $start, mixed $end): self
    {
        $this->where[] = [
            'type' => 'raw',
            'sql' => "`{$column}` BETWEEN ? AND ?",
        ];
        $this->params[] = $start;
        $this->params[] = $end;

        return $this;
    }

    /**
     * Add a raw WHERE clause with optional bindings.
     */
    public function whereRaw(string $sql, array $bindings = []): self
    {
        $this->where[] = ['type' => 'raw', 'sql' => $sql];
        $this->params = array_merge($this->params, $bindings);

        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "INNER JOIN `{$table}` ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN `{$table}` ON {$first} {$operator} {$second}";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        $this->orderBy[] = "`{$column}` {$direction}";
        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groupBy = $columns;
        return $this;
    }

    public function having(string $condition): self
    {
        $this->having = $condition;
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute SELECT and return all rows.
     */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        return $this->db->fetchAll($sql, $this->buildParams());
    }

    /**
     * Execute SELECT and return first row.
     */
    public function first(): ?array
    {
        $this->limit = 1;
        $sql = $this->buildSelectQuery();
        return $this->db->fetch($sql, $this->buildParams());
    }

    /**
     * Execute COUNT query.
     */
    public function count(): int
    {
        $originalSelect = $this->select;
        $this->select = ['COUNT(*) as total'];

        $sql = $this->buildSelectQuery();
        $result = $this->db->fetch($sql, $this->buildParams());

        $this->select = $originalSelect;

        return (int) ($result['total'] ?? 0);
    }

    /**
     * Check if any rows exist.
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Insert a row and return the ID.
     */
    public function insertGetId(array $data): int
    {
        if ($this->tenantId !== null && !isset($data[$this->tenantColumn])) {
            $data[$this->tenantColumn] = $this->tenantId;
        }

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update rows matching the current conditions.
     */
    public function updateRows(array $data): int
    {
        $setClauses = [];
        $params = [];

        foreach ($data as $column => $value) {
            $setClauses[] = "`{$column}` = ?";
            $params[] = $value;
        }

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $setClauses);
        $sql .= $this->buildWhereClause();

        $params = array_merge($params, $this->buildParams());

        return $this->db->query($sql, $params)->rowCount();
    }

    /**
     * Delete rows matching the current conditions.
     */
    public function deleteRows(): int
    {
        $sql = "DELETE FROM `{$this->table}`" . $this->buildWhereClause();
        return $this->db->query($sql, $this->buildParams())->rowCount();
    }

    /**
     * Get paginated results.
     */
    public function paginate(int $page = 1, int $perPage = 20): array
    {
        $total = $this->count();

        $this->limit = $perPage;
        $this->offset = ($page - 1) * $perPage;

        $data = $this->get();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    // -- Private query building --

    private function buildSelectQuery(): string
    {
        $select = implode(', ', $this->select);
        $sql = "SELECT {$select} FROM `{$this->table}`";

        foreach ($this->joins as $join) {
            $sql .= " {$join}";
        }

        $sql .= $this->buildWhereClause();

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY " . implode(', ', array_map(fn($c) => "`{$c}`", $this->groupBy));
        }

        if ($this->having !== null) {
            $sql .= " HAVING {$this->having}";
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    private function buildWhereClause(): string
    {
        $conditions = [];

        // Tenant filter (always first)
        if ($this->tenantId !== null) {
            $conditions[] = "`{$this->table}`.`{$this->tenantColumn}` = ?";
        }

        foreach ($this->where as $clause) {
            if ($clause['type'] === 'raw') {
                $conditions[] = $clause['sql'];
            } else {
                $conditions[] = "`{$clause['column']}` {$clause['operator']} ?";
            }
        }

        if (empty($conditions)) {
            return '';
        }

        return ' WHERE ' . implode(' AND ', $conditions);
    }

    private function buildParams(): array
    {
        $params = [];

        if ($this->tenantId !== null) {
            $params[] = $this->tenantId;
        }

        return array_merge($params, $this->params);
    }
}

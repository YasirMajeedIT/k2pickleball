<?php

declare(strict_types=1);

namespace App\Modules\AuditLogs;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Security\Sanitizer;
use App\Core\Exceptions\NotFoundException;

final class AuditLogController extends Controller
{
    private AuditLogRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new AuditLogRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request, 50);

        $filters = [];
        if ($request->input('user_id')) {
            $filters['user_id'] = Sanitizer::integer($request->input('user_id'));
        }
        if ($request->input('action')) {
            $filters['action'] = Sanitizer::string($request->input('action'));
        }
        if ($request->input('entity_type')) {
            $filters['entity_type'] = Sanitizer::string($request->input('entity_type'));
        }
        if ($request->input('date_from')) {
            $filters['date_from'] = Sanitizer::string($request->input('date_from'));
        }
        if ($request->input('date_to')) {
            $filters['date_to'] = Sanitizer::string($request->input('date_to'));
        }

        $result = $this->repo->findByOrganization($orgId, $filters, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $log = $this->repo->findById($id);
        if (!$log) {
            throw new NotFoundException('Audit log entry not found');
        }
        return $this->success($log);
    }

    public function entity(Request $request, string $entityType, int $entityId): Response
    {
        [$page, $perPage] = $this->pagination($request, 50);
        $entityType = Sanitizer::string($entityType);

        $result = $this->repo->findByEntity($entityType, $entityId, $page, $perPage);
        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Labels;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class LabelController extends Controller
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId() ?? (int) $request->input('organization_id', 0);
        if (!$orgId) {
            return $this->success([]);
        }

        $labels = $this->db->fetchAll(
            "SELECT * FROM labels WHERE organization_id = ? ORDER BY name ASC",
            [$orgId]
        );

        return $this->success($labels);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
        ]);

        $orgId = $request->organizationId() ?? (int) ($data['organization_id'] ?? 0);
        if (!$orgId) {
            return $this->error('Organization context required', 422);
        }

        $id = $this->db->insert('labels', [
            'organization_id' => $orgId,
            'name'            => Sanitizer::string($data['name']),
            'color'           => $data['color'] ?? '#6366f1',
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        $label = $this->db->fetch("SELECT * FROM labels WHERE id = ?", [$id]);
        return $this->success($label, 'Label created');
    }

    public function update(Request $request, int $id): Response
    {
        $label = $this->db->fetch("SELECT * FROM labels WHERE id = ?", [$id]);
        if (!$label) {
            throw new NotFoundException('Label not found');
        }

        $data = $request->all();
        $update = [];
        if (isset($data['name'])) {
            $update['name'] = Sanitizer::string($data['name']);
        }
        if (isset($data['color'])) {
            $update['color'] = $data['color'];
        }

        if ($update) {
            $this->db->update('labels', $update, ['id' => $id]);
        }

        $label = $this->db->fetch("SELECT * FROM labels WHERE id = ?", [$id]);
        return $this->success($label, 'Label updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $label = $this->db->fetch("SELECT * FROM labels WHERE id = ?", [$id]);
        if (!$label) {
            throw new NotFoundException('Label not found');
        }

        $this->db->delete('labels', ['id' => $id]);
        return $this->success(null, 'Label deleted');
    }
}

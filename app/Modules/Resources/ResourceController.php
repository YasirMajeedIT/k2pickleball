<?php

declare(strict_types=1);

namespace App\Modules\Resources;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class ResourceController extends Controller
{
    private ResourceRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new ResourceRepository($db);
    }

    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));

        $result = $this->repo->findByOrganization($orgId, $search ?: null, $page, $perPage);

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    public function show(Request $request, int $id): Response
    {
        $resource = $this->repo->findWithValues($id);
        if (!$resource) {
            throw new NotFoundException('Resource not found');
        }
        return $this->success($resource);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'field_type'  => 'nullable|string',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['field_type'] = in_array($data['field_type'] ?? '', ['checkbox', 'selectbox', 'radio', 'input']) ? $data['field_type'] : 'checkbox';
        $orgId = $request->organizationId();

        if ($this->repo->nameExists($orgId, $data['name'])) {
            return $this->validationError(['name' => ['Resource name already exists']]);
        }

        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $orgId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $resource = $this->repo->findWithValues($id);

        return $this->created($resource, 'Resource created');
    }

    public function update(Request $request, int $id): Response
    {
        $resource = $this->repo->findById($id);
        if (!$resource) {
            throw new NotFoundException('Resource not found');
        }

        $data = Validator::validate($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'field_type'  => 'nullable|string',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['field_type'] = in_array($data['field_type'] ?? '', ['checkbox', 'selectbox', 'radio', 'input']) ? $data['field_type'] : 'checkbox';
        $orgId = $request->organizationId();

        if ($this->repo->nameExists($orgId, $data['name'], $id)) {
            return $this->validationError(['name' => ['Resource name already exists']]);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->repo->update($id, $data);
        $resource = $this->repo->findWithValues($id);

        return $this->success($resource, 'Resource updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $resource = $this->repo->findById($id);
        if (!$resource) {
            throw new NotFoundException('Resource not found');
        }

        $this->repo->deleteValuesByResource($id);
        $this->repo->delete($id);
        return $this->success(null, 'Resource deleted');
    }

    // --- Resource Values ---

    public function storeValue(Request $request, int $id): Response
    {
        $resource = $this->repo->findById($id);
        if (!$resource) {
            throw new NotFoundException('Resource not found');
        }

        $data = Validator::validate($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        if (!isset($data['sort_order']) || $data['sort_order'] === '') {
            $data['sort_order'] = $this->repo->getMaxValueSortOrder($id) + 1;
        }

        $data['uuid'] = $this->generateUuid();
        $data['resource_id'] = $id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->repo->createValue($data);
        $resource = $this->repo->findWithValues($id);

        return $this->created($resource, 'Value added');
    }

    public function updateValue(Request $request, int $id, int $valueId): Response
    {
        $value = $this->repo->findValueById($valueId);
        if (!$value || (int) $value['resource_id'] !== $id) {
            throw new NotFoundException('Value not found');
        }

        $data = Validator::validate($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->repo->updateValue($valueId, $data);
        $resource = $this->repo->findWithValues($id);

        return $this->success($resource, 'Value updated');
    }

    public function destroyValue(Request $request, int $id, int $valueId): Response
    {
        $value = $this->repo->findValueById($valueId);
        if (!$value || (int) $value['resource_id'] !== $id) {
            throw new NotFoundException('Value not found');
        }

        $this->repo->deleteValue($valueId);
        $resource = $this->repo->findWithValues($id);

        return $this->success($resource, 'Value removed');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

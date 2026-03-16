<?php

declare(strict_types=1);

namespace App\Modules\Categories;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;
use App\Core\Exceptions\NotFoundException;

final class CategoryController extends Controller
{
    private CategoryRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new CategoryRepository($db);
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
        $category = $this->repo->findById($id);
        if (!$category) {
            throw new NotFoundException('Category not found');
        }
        return $this->success($category);
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->all(), [
            'name'       => 'required|string|max:255',
            'color'      => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer',
            'is_taxable' => 'nullable|boolean',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $orgId = $request->organizationId();

        if ($this->repo->nameExists($orgId, $data['name'])) {
            return $this->validationError(['name' => ['Category name already exists']]);
        }

        if (!isset($data['sort_order']) || $data['sort_order'] === '') {
            $data['sort_order'] = $this->repo->getMaxSortOrder($orgId) + 1;
        }
        $data['is_taxable'] = !empty($data['is_taxable']) ? 1 : 0;
        $data['uuid'] = $this->generateUuid();
        $data['organization_id'] = $orgId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->repo->create($data);
        $category = $this->repo->findById($id);

        return $this->created($category, 'Category created');
    }

    public function update(Request $request, int $id): Response
    {
        $category = $this->repo->findById($id);
        if (!$category) {
            throw new NotFoundException('Category not found');
        }

        $data = Validator::validate($request->all(), [
            'name'       => 'required|string|max:255',
            'color'      => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer',
            'is_taxable' => 'nullable|boolean',
        ]);

        $data['name'] = Sanitizer::string($data['name']);
        $orgId = $request->organizationId();

        if ($this->repo->nameExists($orgId, $data['name'], $id)) {
            return $this->validationError(['name' => ['Category name already exists']]);
        }

        $data['is_taxable'] = !empty($data['is_taxable']) ? 1 : 0;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->repo->update($id, $data);
        $category = $this->repo->findById($id);

        return $this->success($category, 'Category updated');
    }

    public function destroy(Request $request, int $id): Response
    {
        $category = $this->repo->findById($id);
        if (!$category) {
            throw new NotFoundException('Category not found');
        }

        $this->repo->delete($id);
        return $this->success(null, 'Category deleted');
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

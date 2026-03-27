<?php

declare(strict_types=1);

namespace App\Modules\CustomPages;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Database\Connection;

final class CustomPageController extends Controller
{
    private CustomPageRepository $repo;

    public function __construct(Connection $db)
    {
        parent::__construct();
        $this->repo = new CustomPageRepository($db);
    }

    /* ═══ Admin CRUD ═══ */

    /** GET /api/custom-pages */
    public function index(Request $request): Response
    {
        $orgId = $request->organizationId();
        return $this->success($this->repo->findAllForOrg($orgId));
    }

    /** GET /api/custom-pages/{id} */
    public function show(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $page = $this->repo->findById($orgId, $id);
        if (!$page) return $this->error('Page not found', 404);
        return $this->success($page);
    }

    /** POST /api/custom-pages */
    public function store(Request $request): Response
    {
        $orgId = $request->organizationId();
        $input = $request->all();

        Validator::validate($input, [
            'title' => 'required|string|max:255',
        ]);

        $title = Sanitizer::string($input['title']);
        $slug = Sanitizer::slug($input['slug'] ?? $title);

        // Ensure unique slug
        $existing = $this->repo->findBySlug($orgId, $slug);
        if ($existing) {
            $slug .= '-' . time();
        }

        // Sanitize HTML content — allow safe HTML tags
        $content = $input['content'] ?? '';
        $content = $this->sanitizeHtml($content);

        $id = $this->repo->createPage($orgId, [
            'title'           => $title,
            'slug'            => $slug,
            'content'         => $content,
            'meta_description'=> Sanitizer::string($input['meta_description'] ?? ''),
            'status'          => $input['status'] ?? 'draft',
            'show_in_nav'     => $input['show_in_nav'] ?? 0,
            'show_in_footer'  => $input['show_in_footer'] ?? 0,
            'sort_order'      => $input['sort_order'] ?? 0,
            'created_by'      => $request->userId(),
        ]);

        return $this->success($this->repo->findById($orgId, $id), 'Page created', 201);
    }

    /** PUT /api/custom-pages/{id} */
    public function update(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $page = $this->repo->findById($orgId, $id);
        if (!$page) return $this->error('Page not found', 404);

        $data = $request->all();

        if (isset($data['title'])) $data['title'] = Sanitizer::string($data['title']);
        if (isset($data['slug']))  $data['slug']  = Sanitizer::slug($data['slug']);
        if (isset($data['meta_description'])) $data['meta_description'] = Sanitizer::string($data['meta_description']);
        if (isset($data['content'])) $data['content'] = $this->sanitizeHtml($data['content']);

        // Unique slug check
        if (isset($data['slug']) && $data['slug'] !== $page['slug']) {
            $existing = $this->repo->findBySlug($orgId, $data['slug']);
            if ($existing && $existing['id'] != $id) {
                return $this->validationError(['slug' => ['This slug is already taken']]);
            }
        }

        $this->repo->updatePage($orgId, $id, $data);
        return $this->success($this->repo->findById($orgId, $id), 'Page updated');
    }

    /** DELETE /api/custom-pages/{id} */
    public function destroy(Request $request, int $id): Response
    {
        $orgId = $request->organizationId();
        $page = $this->repo->findById($orgId, $id);
        if (!$page) return $this->error('Page not found', 404);

        $this->repo->deletePage($orgId, $id);
        return $this->success(null, 'Page deleted');
    }

    /* ═══ Public ═══ */

    /** GET /api/public/pages/{slug} */
    public function publicShow(Request $request, string $slug): Response
    {
        $orgId = $request->organizationId();
        if (!$orgId) return $this->error('Organization not found', 404);

        $page = $this->repo->findPublished($orgId, $slug);
        if (!$page) return $this->error('Page not found', 404);

        return $this->success([
            'title'           => $page['title'],
            'slug'            => $page['slug'],
            'content'         => $page['content'],
            'meta_description'=> $page['meta_description'],
        ]);
    }

    /** GET /api/public/pages — list published pages (for nav/footer) */
    public function publicIndex(Request $request): Response
    {
        $orgId = $request->organizationId();
        if (!$orgId) return $this->error('Organization not found', 404);

        $navPages = $this->repo->getNavPages($orgId);
        $footerPages = $this->repo->getFooterPages($orgId);

        return $this->success([
            'nav'    => $navPages,
            'footer' => $footerPages,
        ]);
    }

    /* ── Helpers ── */

    /**
     * Sanitize HTML content — allow safe structural tags, strip scripts and events.
     */
    private function sanitizeHtml(string $html): string
    {
        // Remove script/style tags and their contents
        $html = preg_replace('#<script[^>]*>.*?</script>#is', '', $html);
        $html = preg_replace('#<style[^>]*>.*?</style>#is', '', $html);
        // Remove event attributes (onclick, onerror, etc.)
        $html = preg_replace('#\s+on\w+\s*=\s*["\'][^"\']*["\']#i', '', $html);
        $html = preg_replace('#\s+on\w+\s*=\s*\S+#i', '', $html);
        // Remove javascript: protocol in attributes
        $html = preg_replace('#(href|src|action)\s*=\s*["\']?\s*javascript:#i', '$1="', $html);
        return $html;
    }
}

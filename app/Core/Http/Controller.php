<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Services\Container;

/**
 * Base controller with common helper methods.
 * All module controllers extend this class.
 */
abstract class Controller
{
    protected Container $container;
    protected Request $request;

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * Set the current request (called by the dispatcher).
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Return a JSON success response.
     */
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): Response
    {
        return Response::success($data, $message, $status);
    }

    /**
     * Return a JSON error response.
     */
    protected function error(string $message, int $status = 400, array $errors = []): Response
    {
        return Response::error($message, $status, $errors);
    }

    /**
     * Return a created response.
     */
    protected function created(mixed $data = null, string $message = 'Created successfully'): Response
    {
        return Response::created($data, $message);
    }

    /**
     * Return a no content response.
     */
    protected function noContent(): Response
    {
        return Response::noContent();
    }

    /**
     * Return a paginated response.
     */
    protected function paginated(array $data, int $total, int $page, int $perPage): Response
    {
        return Response::paginated($data, $total, $page, $perPage);
    }

    /**
     * Return a validation error response.
     */
    protected function validationError(array $errors, string $message = 'Validation failed'): Response
    {
        return Response::validationError($errors, $message);
    }

    /**
     * Get the current tenant/organization ID.
     */
    protected function organizationId(): ?int
    {
        return $this->request->organizationId();
    }

    /**
     * Get the current authenticated user ID.
     */
    protected function userId(): ?int
    {
        return $this->request->userId();
    }

    /**
     * Get pagination parameters from query string.
     * Returns [$page, $perPage] for easy destructuring.
     */
    protected function pagination(?Request $request = null, int $defaultPerPage = 20): array
    {
        $req = $request ?? $this->request;
        $page = max(1, (int) ($req->query('page', '1')));
        $perPage = min(100, max(1, (int) ($req->query('per_page', (string) $defaultPerPage))));

        return [$page, $perPage];
    }

    /**
     * Get sort parameters from query string.
     */
    protected function sorting(string $defaultField = 'created_at', string $defaultDirection = 'DESC'): array
    {
        $field = $this->request->query('sort', $defaultField);
        $direction = strtoupper($this->request->query('direction', $defaultDirection));

        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'DESC';
        }

        return [
            'field' => $field,
            'direction' => $direction,
        ];
    }
}

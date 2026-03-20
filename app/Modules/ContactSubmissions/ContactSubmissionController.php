<?php

declare(strict_types=1);

namespace App\Modules\ContactSubmissions;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Services\SlackNotifier;
use App\Core\Exceptions\NotFoundException;

final class ContactSubmissionController extends Controller
{
    private ContactSubmissionRepository $repo;

    public function __construct(Connection $db)
    {
        $this->repo = new ContactSubmissionRepository($db);
    }

    /**
     * PUBLIC endpoint — submit a contact form message.
     */
    public function submit(Request $request): Response
    {
        Validator::validate($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'subject'    => 'required|in:partnership,demo,support,press,other',
            'message'    => 'required|string|max:5000',
        ]);

        $data = [
            'first_name' => Sanitizer::string($request->input('first_name')),
            'last_name'  => Sanitizer::string($request->input('last_name')),
            'email'      => Sanitizer::email($request->input('email')),
            'subject'    => $request->input('subject'),
            'message'    => Sanitizer::string($request->input('message')),
            'status'     => 'new',
            'ip_address' => $request->ip(),
        ];

        $id = $this->repo->create($data);

        // Fire-and-forget Slack notification
        try {
            SlackNotifier::getInstance()->sendContactNotification($data);
        } catch (\Throwable $e) {
            error_log('[Contact] Slack notification failed: ' . $e->getMessage());
        }

        return $this->created(['id' => $id, 'message' => 'Message sent successfully']);
    }

    /**
     * Platform admin — list all contact submissions.
     */
    public function index(Request $request): Response
    {
        [$page, $perPage] = $this->pagination($request);
        $search  = Sanitizer::string($request->input('search', ''));
        $status  = Sanitizer::string($request->input('status', ''));
        $subject = Sanitizer::string($request->input('subject', ''));

        $result = $this->repo->findAllPaginated(
            $search ?: null,
            $status ?: null,
            $subject ?: null,
            $page,
            $perPage
        );

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    /**
     * Platform admin — view single contact submission.
     */
    public function show(Request $request, int $id): Response
    {
        $item = $this->repo->findById($id);
        if (!$item) {
            throw new NotFoundException('Contact submission not found');
        }
        return $this->success($item);
    }

    /**
     * Platform admin — update submission status/notes.
     */
    public function updateStatus(Request $request, int $id): Response
    {
        $item = $this->repo->findById($id);
        if (!$item) {
            throw new NotFoundException('Contact submission not found');
        }

        Validator::validate($request->all(), [
            'status' => 'required|in:new,read,replied,archived',
            'notes'  => 'string|max:5000',
        ]);

        $this->repo->updateStatus(
            $id,
            $request->input('status'),
            Sanitizer::string($request->input('notes', ''))
        );

        return $this->success(['message' => 'Contact submission updated']);
    }

    /**
     * Platform admin — delete a contact submission.
     */
    public function destroy(Request $request, int $id): Response
    {
        $item = $this->repo->findById($id);
        if (!$item) {
            throw new NotFoundException('Contact submission not found');
        }

        $this->repo->delete($id);
        return $this->noContent();
    }

    /**
     * Platform admin — contact submission stats.
     */
    public function stats(Request $request): Response
    {
        return $this->success($this->repo->stats());
    }
}

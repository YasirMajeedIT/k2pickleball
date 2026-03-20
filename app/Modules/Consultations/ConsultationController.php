<?php

declare(strict_types=1);

namespace App\Modules\Consultations;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Database\Connection;
use App\Core\Security\Sanitizer;
use App\Core\Security\Validator;
use App\Core\Services\SlackNotifier;
use App\Core\Exceptions\NotFoundException;

final class ConsultationController extends Controller
{
    private ConsultationRepository $repo;

    public function __construct(Connection $db)
    {
        $this->repo = new ConsultationRepository($db);
    }

    /**
     * PUBLIC endpoint — submit a consultation request.
     * No authentication required (public demo/contact form).
     */
    public function submit(Request $request): Response
    {
        Validator::validate($request->all(), [
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email|max:255',
            'phone'             => 'string|max:30',
            'consultation_type' => 'required|in:partnership,software_integration,general',
            'facility_stage'    => 'string|max:50',
            'planned_location'  => 'string|max:200',
            'number_of_courts'  => 'string|max:20',
            'software_interest' => 'string|max:255',
            'message'           => 'string|max:5000',
        ]);

        $data = [
            'first_name'        => Sanitizer::string($request->input('first_name')),
            'last_name'         => Sanitizer::string($request->input('last_name')),
            'email'             => Sanitizer::email($request->input('email')),
            'phone'             => Sanitizer::phone($request->input('phone', '')),
            'consultation_type' => $request->input('consultation_type'),
            'facility_stage'    => Sanitizer::string($request->input('facility_stage', '')),
            'planned_location'  => Sanitizer::string($request->input('planned_location', '')),
            'number_of_courts'  => Sanitizer::string($request->input('number_of_courts', '')),
            'software_interest' => Sanitizer::string($request->input('software_interest', '')),
            'message'           => Sanitizer::string($request->input('message', '')),
            'status'            => 'new',
        ];

        $id = $this->repo->create($data);

        // Fire-and-forget Slack notification
        try {
            SlackNotifier::getInstance()->sendConsultationNotification($data);
        } catch (\Throwable $e) {
            error_log('[Consultation] Slack notification failed: ' . $e->getMessage());
        }

        return $this->created(['id' => $id, 'message' => 'Consultation request submitted successfully']);
    }

    /**
     * Platform admin — list all consultation requests.
     */
    public function index(Request $request): Response
    {
        [$page, $perPage] = $this->pagination($request);
        $search = Sanitizer::string($request->input('search', ''));
        $status = Sanitizer::string($request->input('status', ''));
        $type   = Sanitizer::string($request->input('type', ''));

        $result = $this->repo->findAllPaginated(
            $search ?: null,
            $status ?: null,
            $type ?: null,
            $page,
            $perPage
        );

        return $this->paginated($result['data'], $result['total'], $page, $perPage);
    }

    /**
     * Platform admin — view single consultation.
     */
    public function show(Request $request, int $id): Response
    {
        $consultation = $this->repo->findById($id);
        if (!$consultation) {
            throw new NotFoundException('Consultation not found');
        }
        return $this->success($consultation);
    }

    /**
     * Platform admin — update consultation status/notes.
     */
    public function updateStatus(Request $request, int $id): Response
    {
        $consultation = $this->repo->findById($id);
        if (!$consultation) {
            throw new NotFoundException('Consultation not found');
        }

        Validator::validate($request->all(), [
            'status' => 'required|in:new,contacted,in_progress,closed',
            'notes'  => 'string|max:5000',
        ]);

        $this->repo->updateStatus(
            $id,
            $request->input('status'),
            Sanitizer::string($request->input('notes', ''))
        );

        return $this->success(['message' => 'Consultation updated']);
    }

    /**
     * Platform admin — delete a consultation request.
     */
    public function destroy(Request $request, int $id): Response
    {
        $consultation = $this->repo->findById($id);
        if (!$consultation) {
            throw new NotFoundException('Consultation not found');
        }

        $this->repo->delete($id);
        return $this->noContent();
    }

    /**
     * Platform admin — consultation stats.
     */
    public function stats(Request $request): Response
    {
        return $this->success($this->repo->stats());
    }
}

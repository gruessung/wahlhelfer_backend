<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\SessionEntry;
use App\Repository\SessionEntryRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class SessionController extends AbstractController
{
    public function __construct(readonly EntityManagerInterface $entityManager, readonly SessionRepository $sessionRepository, readonly SessionEntryRepository $sessionEntryRepository, readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    private function authenticate($hash, Request $request) : bool
    {
        if (empty($hash)) { return false; }
        if (is_null($request->get('password'))) return false;

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'sodium' => ['algorithm' => 'sodium'],
        ]);

        $hasher = $factory->getPasswordHasher('common');
        return $hasher->verify($hash, $request->get('password'));
    }

    #[Route('/session/{session_id}/join', name: 'session_join', methods: ['POST'])]
    public function join(String $session_id, Request $request) : Response {
        $session = $this->sessionRepository->findOneBy(['session_uuid' => $session_id]);
        if (is_null($session)) {
            return new JsonResponse(['status'=> "bad request", "message" => "session not found"], 400);
        }

        if (!$this->authenticate($session->getPassword(), $request)) return new JsonResponse(['status'=> "login required"], 401);

        return new JsonResponse($session->asArray());
    }

    #[Route('/session/create', name: 'session_create', methods: ['POST'])]
    public function create(Request $request): Response {
        $uuid = Uuid::v4();
        $cnt_pages = $request->request->get('cnt_pages');
        $cnt_entries_per_page = $request->request->get('cnt_entries_per_page');
        $cnt_entries_total = $request->request->get('cnt_entries_total');
        $password = $request->request->get('password');

        if (empty($cnt_pages) || empty($cnt_entries_per_page) || empty($cnt_entries_total)) {
            return new JsonResponse(['status'=> "bad request", "message" => "required data missing"], 400);
        }

        if (($cnt_entries_per_page * $cnt_pages ) < $cnt_entries_total) {
            return new JsonResponse(["status" => "bad request", "message" => "logical failure"], 400);
        }

        if (strlen($password) < 10) {
            return new JsonResponse(["status" => "bad request", "message" => "password too short, minimum 10 chars"], 400);
        }

        $name = implode(" ",json_decode(file_get_contents('https://random-word-api.herokuapp.com/word?number=3&lang=de'), true));

        $session = new Session();
        $session->setSessionUuid($uuid);
        $session->setName($name);

        $session->setCntEntriesPerPage($cnt_entries_per_page);
        $session->setCntEntriesTotal($cnt_entries_total);


        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'sodium' => ['algorithm' => 'sodium'],
        ]);

        $hasher = $factory->getPasswordHasher('common');
        $hash = $hasher->hash($password);
        $session->setPassword($hash);


        $entry_no = 1;
        $pages = 0;

        for($i = 1; $i <= $cnt_pages; $i++) {
            for ($j = 1; $j <= $cnt_entries_per_page; $j++) {
                if ($entry_no > $cnt_entries_total) continue;
                if ($pages != $i) $pages = $i;
                $entry = new SessionEntry();
                $entry->setSession($session);
                $entry->setEntryNo($entry_no);
                $entry->setPageNo($i);
                $entry->setMark1(false);
                $entry->setMark2(false);
                $this->entityManager->persist($entry);
                $entry_no++;
            }
        }
        $session->setCntPages($pages);
        $this->entityManager->persist($session);

        $this->entityManager->flush();

        return new JsonResponse($session->asArray());
    }

    #[Route('/session/{session_id}/{entry_no}/mark/{mark_type}', name: 'session_mark_toggle', methods: ['POST'])]
    public function mark1(string $session_id, int $entry_no, int $mark_type, Request $request) : Response {


        if ($mark_type !== 1 && $mark_type !== 2) {
            return new JsonResponse(['status'=> "bad request", "message" => "invalid mark_type"], 400);
        }

        $session = $this->sessionRepository->findOneBy(['session_uuid' => $session_id]);
        if (is_null($session)) {
            return new JsonResponse(['status'=> "bad request", "message" => "session not found"], 400);
        }

        if (!$this->authenticate($session->getPassword(), $request)) return new JsonResponse(['status'=> "login required"], 401);

        $entry = $this->sessionEntryRepository->findOneBy(['session' => $session, 'entry_no' => $entry_no]);
        if (is_null($entry)) {
            return new JsonResponse(['status'=> "bad request", "message" => "sessionentry not found"], 400);
        }

        if ($entry->isMark1() && $entry->isMark2()) {
            return new JsonResponse(['status' => "ok", "message" => "entry already finished voting"], 201);
        }

        if ($mark_type === 1) {
            $entry->setMark1(!$entry->isMark1());
        }
        if ($mark_type === 2) {
            $entry->setMark2(!$entry->isMark2());
        }

        $this->entityManager->persist($entry);
        $this->entityManager->flush();

        return new JsonResponse(['status'=> "ok", "data" => $entry->asArray()], 200);
    }
    #[Route('/session/{session_id}', name: 'session_get', methods: ['POST'])]
    public function get(string $session_id, Request $request): Response {
        $session = $this->sessionRepository->findOneBy(['session_uuid' => $session_id]);
        if (is_null($session)) {
            return new JsonResponse(['status'=> "bad request", "message" => "session not found"], 400);
        }
        if (!$this->authenticate($session->getPassword(), $request)) return new JsonResponse(['status'=> "login required"], 401);

        return new JsonResponse($session->asArray());
    }

}

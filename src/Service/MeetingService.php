<?php

namespace App\Service;

use App\Entity\Meeting;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class MeetingService
{
    private EntityManagerInterface $entityManager;
    private MeetingRepository $meetingRepository;

    public function __construct(EntityManagerInterface $entityManager, MeetingRepository $meetingRepository)
    {
        $this->entityManager = $entityManager;
        $this->meetingRepository = $meetingRepository;
    }

    public function getAllMeetings(): array
    {
        return $this->meetingRepository->findAll();
    }

    public function createMeeting(Meeting $meeting): void
    {
        $this->entityManager->persist($meeting);
        $this->entityManager->flush();
    }

    public function updateMeeting(): void
    {
        $this->entityManager->flush();
    }

    public function deleteMeeting(Meeting $meeting): void
    {
        $this->entityManager->remove($meeting);
        $this->entityManager->flush();
    }
}

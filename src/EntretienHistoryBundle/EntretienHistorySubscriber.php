<?php

namespace App\EventSubscriber;

use App\Entity\Entretien;
use App\Entity\EntretienHistory;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;

class EntretienHistorySubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [Events::onFlush];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();

        $em = $args->getObjectManager();
        
        if (!$em instanceof EntityManagerInterface) {
            return;
        }

        $uow = $em->getUnitOfWork();
        

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Entretien) {
                $this->logHistory($em, $uow, $entity, 'Create');
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Entretien) {
                $changes = $uow->getEntityChangeSet($entity);

                foreach ($changes as $field => [$oldValue, $newValue]) {
                    $this->logFieldChange($em, $uow, $entity, 'Update', $field, $oldValue, $newValue);
                }
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof Entretien) {
                $this->logHistory($em, $uow, $entity, 'Delete');
            }
        }
    }

    private function logHistory($em, $uow, Entretien $entretien, string $action)
    {
        $history = new EntretienHistory();
        $history->setAction($action);
        $history->setFieldChanged('ALL');
    
        if ($action === 'Delete') {
            $oldValue = json_encode([
                'title' => $entretien->getTitre(),
                'description' => $entretien->getDescription(),
            ]);
            $history->setOldValue($oldValue ?? '');
            $history->setNewValue('');
        } elseif ($action === 'Create') {
            $newValue = json_encode([
                'title' => $entretien->getTitre(),
                'description' => $entretien->getDescription(),
            ]);
            $history->setOldValue('');
            $history->setNewValue($newValue ?? '');
        }
    
        $history->setDate(new \DateTimeImmutable());
        $history->setEntretien($entretien);
    
        $em->persist($history);
        $uow->computeChangeSet($em->getClassMetadata(EntretienHistory::class), $history);
    }
    

    private function logFieldChange($em, $uow, Entretien $entretien, string $action, string $field, $oldValue, $newValue)
    {
        $history = new EntretienHistory();
        $history->setAction($action);
        $history->setFieldChanged($field);
        $history->setOldValue(is_scalar($oldValue) ? (string) $oldValue : json_encode($oldValue));
        $history->setNewValue(is_scalar($newValue) ? (string) $newValue : json_encode($newValue));
        $history->setDate(new \DateTimeImmutable());
        $history->setEntretien($entretien);

        $em->persist($history);
        $uow->computeChangeSet($em->getClassMetadata(EntretienHistory::class), $history);
    }
}

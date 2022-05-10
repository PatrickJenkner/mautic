<?php

declare(strict_types=1);

namespace Mautic\LeadBundle\EventListener;

use Mautic\LeadBundle\Entity\ContactExportScheduler;
use Mautic\LeadBundle\Event\ContactExportSchedulerEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\ContactExportSchedulerModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactScheduledExportSubscriber implements EventSubscriberInterface
{
    private ContactExportSchedulerModel $contactExportSchedulerModel;

    public function __construct(ContactExportSchedulerModel $contactExportSchedulerModel)
    {
        $this->contactExportSchedulerModel = $contactExportSchedulerModel;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LeadEvents::CONTACT_EXPORT_PREPARE_FILE    => 'onContactExportPrepareFile',
            LeadEvents::CONTACT_EXPORT_SEND_EMAIL      => 'onContactExportSendEmail',
            LeadEvents::POST_CONTACT_EXPORT_SEND_EMAIL => 'onContactExportEmailSent',
        ];
    }

    public function onContactExportPrepareFile(ContactExportSchedulerEvent $event): void
    {
        $contactExportScheduler = $event->getContactExportScheduler();
        \assert($contactExportScheduler instanceof ContactExportScheduler);
        $filePath = $this->contactExportSchedulerModel->processAndGetExportFilePath($contactExportScheduler);
        $event->setFilePath($filePath);
    }

    public function onContactExportSendEmail(ContactExportSchedulerEvent $event): void
    {
        $contactExportScheduler = $event->getContactExportScheduler();
        \assert($contactExportScheduler instanceof ContactExportScheduler);
        $this->contactExportSchedulerModel->sendEmail($contactExportScheduler, $event->getFilePath());
    }

    public function onContactExportEmailSent(ContactExportSchedulerEvent $event): void
    {
        $contactExportScheduler = $event->getContactExportScheduler();
        \assert($contactExportScheduler instanceof ContactExportScheduler);
        $this->contactExportSchedulerModel->deleteEntity($contactExportScheduler);
    }
}

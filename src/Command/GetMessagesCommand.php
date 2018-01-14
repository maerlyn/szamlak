<?php

namespace App\Command;

use App\Entity\Invoice;
use App\Service\GoogleClientService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetMessagesCommand extends Command
{
    protected $service;
    protected $dataPath;
    protected $em;

    protected static $defaultName = "szamlak:get-messages";

    public function __construct(GoogleClientService $clientService, $dataPath, EntityManager $em)
    {
        parent::__construct();

        $this->service = $clientService->getGmailService();
        $this->dataPath = $dataPath;
        $this->em = $em;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $user = "me";

        $result = $this->service->users_messages->listUsersMessages($user, [
            "q" => "is:unread",
        ]);

        $output->write("<info>" . $result->count() . "</info> unread messages\n");

        foreach ($result->getMessages() as $message) {
            /** @var $message \Google_Service_Gmail_Message */
            $message = $this->service->users_messages->get($user, $message->getId());

            $parts = $message->getPayload()->getParts();

            foreach ($parts as $part) {
                /** @var $part \Google_Service_Gmail_MessagePart */
                if (!$part->getFilename()) continue;

                $attachmentId = $part->getBody()->getAttachmentId();
                $attachmentPart = $this->service->users_messages_attachments->get($user, $message->getId(), $attachmentId);

                //replace because of base64 url encoding
                $data = str_replace(['-', '_'], ['+', '/'], $attachmentPart->getData());
                file_put_contents($this->dataPath . "/images/" . sha1($message->getId()), base64_decode($data));

                $invoice = new Invoice();
                $invoice->setMessageId($message->getId());
                $invoice->setFilename(sha1($attachmentId));
                $this->em->persist($invoice);
                $this->em->flush();
            }

            $mod = new \Google_Service_Gmail_ModifyMessageRequest();
            $mod->setRemoveLabelIds(["UNREAD"]);

            $this->service->users_messages->modify($user, $message->getId(), $mod);
        }
    }
}

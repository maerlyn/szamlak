<?php

namespace App\Command;

use Doctrine\ORM\EntityManager;
use Google\Cloud\Vision\VisionClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OCRCommand extends Command
{
    protected $visionClient;
    protected $dataPath;
    protected $em;

    protected static $defaultName = "szamlak:ocr";

    public function __construct(VisionClient $visionClient, $dataPath, EntityManager $em)
    {
        parent::__construct();

        $this->visionClient = $visionClient;
        $this->dataPath = $dataPath;
        $this->em = $em;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->em->getRepository("App:Invoice");
        $invoicesToOCR = $repo->findBy(["isOCRed" => false]);

        foreach ($invoicesToOCR as $invoice) {
            $image = $this->visionClient->image(fopen($this->dataPath . "/images/" .$invoice->getFilename(), "r"), ["TEXT_DETECTION"]);
            $annotations = $this->visionClient->annotate($image);

            $invoice->setOCRResponse(json_encode($annotations->info(), JSON_UNESCAPED_UNICODE));
        }

        $this->em->flush();
    }
}

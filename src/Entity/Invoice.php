<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *     @ORM\Index(name="is_ocred_idx", columns={"is_ocred"})
 * })
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $messageId;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $filename;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isOCRed = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param mixed $messageId
     * @return Invoice
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     * @return Invoice
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getisOCRed()
    {
        return $this->isOCRed;
    }

    /**
     * @param mixed $isOCRed
     * @return Invoice
     */
    public function setIsOCRed($isOCRed)
    {
        $this->isOCRed = $isOCRed;
        return $this;
    }

}

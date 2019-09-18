<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaperRepository")
 * @Vich\Uploadable
 */
class Paper
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="paper", fileNameProperty="paperName", size="paperSize")
     *
     * @var File
     */
    private $paperFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $paperName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $paperSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="papers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     */
    public function setPaperFile(?File $paperFile = null): void
    {
        $this->paperFile = $paperFile;

        if (null !== $paperFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPaperFile(): ?File
    {
        return $this->paperFile;
    }

    public function setPaperName(?string $paperName): void
    {
        $this->paperName = $paperName;
    }

    public function getPaperName(): ?string
    {
        return $this->paperName;
    }

    public function setPaperSize(?int $paperSize): void
    {
        $this->paperSize = $paperSize;
    }

    public function getPaperSize(): ?int
    {
        return $this->paperSize;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticlesRepository")
 * @Vich\Uploadable
 */
class Articles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @Gedmo\Slug(fields={"titre"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @var \DateTime $created_at
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
    */
    private $created_at;

    /**
     * @var \DateTime $updated_at
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $featured_image;

    /**
     * @Vich\UploadableField(mapping="featured_images", fileNameProperty="featured_image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Categories", inversedBy="articles")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MotsCles", inversedBy="articles")
     */
    private $mots_cles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaires", mappedBy="articles", orphanRemoval=true)
     */
    private $commentaires;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->mots_cles = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->titre;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updated_at = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getFeaturedImage()
    {
        return $this->featured_image;
    }

    public function setFeaturedImage($featured_image)
    {
        $this->featured_image = $featured_image;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection|Categories[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categories $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categories $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|MotsCles[]
     */
    public function getMotsCles(): Collection
    {
        return $this->mots_cles;
    }

    public function addMotsCle(MotsCles $motsCle): self
    {
        if (!$this->mots_cles->contains($motsCle)) {
            $this->mots_cles[] = $motsCle;
        }

        return $this;
    }

    public function removeMotsCle(MotsCles $motsCle): self
    {
        if ($this->mots_cles->contains($motsCle)) {
            $this->mots_cles->removeElement($motsCle);
        }

        return $this;
    }

    /**
     * @return Collection|Commentaires[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setArticles($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getArticles() === $this) {
                $commentaire->setArticles(null);
            }
        }

        return $this;
    }
}

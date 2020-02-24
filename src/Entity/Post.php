<?php

namespace App\Entity;

use App\Entity\User\Author;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    public const STATUS_DRAFT = 1;
    public const STATUS_REVIEW = 2;
    public const STATUS_ACTIVE = 3;
    public const STATUS_CLOSED = 4;
    public const STATUS_ARCHIVED = 5;

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT,
        self::STATUS_REVIEW,
        self::STATUS_ACTIVE,
        self::STATUS_CLOSED,
        self::STATUS_ARCHIVED,
    ];

    public const STATUSES = [
        'Draft' => self::STATUS_DRAFT,
        'Review' => self::STATUS_REVIEW,
        'Active' => self::STATUS_ACTIVE,
        'Closed' => self::STATUS_CLOSED,
        'Archived' => self::STATUS_ARCHIVED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"post:show"})
     * @SWG\Property(description="The unique identifier of the post.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post:show"})
     * @SWG\Property(description="The title of the post.")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post:show"})
     * @SWG\Property(description="The description of the post.")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post:show"})
     * @SWG\Property(description="The content of the post.")
     */
    private $body;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"post:show"})
     * @SWG\Property(description="The status of the post.")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="posts")
     * @Groups({"post:show"})
     * @SWG\Property(ref=@Model(type=Category::class))
     */
    private $category;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255)
     * @Groups({"post:show"})
     * @SWG\Property(description="Post slug.")
     */
    private $slug;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"post:show"})
     * @SWG\Property(description="Creating data.")
     */
    private $createdAt;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"post:show"})
     * @SWG\Property(description="Modifying data.")
     */
    private $modifiedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="posts", cascade={"persist", "remove"})
     * @Groups({"post:show"})
     * @SWG\Property(ref=@Model(type=Tag::class))
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\Author", inversedBy="posts")
     * @Groups({"post:show"})
     * @SWG\Property(ref=@Model(type=Author::class))
     */
    private $author;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removePost($this);
        }

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $explanation = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private ?Quiz $quiz = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class, cascade: ['remove', 'persist'], orphanRemoval: false)]
    private Collection $answers;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $choices = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: QuestionMetadata::class, cascade: ['remove', 'persist'], orphanRemoval: false)]
    private Collection $metadatas;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private ?Arc $arc = null;

    public function __construct() {
        $this->answers = new ArrayCollection();
        $this->metadatas = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function setImage(?string $image): static {
        $this->image = $image;

        return $this;
    }

    public function getSlug(): ?string {
        return $this->slug;
    }

    public function setSlug(string $slug): static {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getExplanation(): ?string {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): static {
        $this->explanation = $explanation;

        return $this;
    }

    public function getPosition(): ?int {
        return $this->position;
    }

    public function setPosition(int $position): static {
        $this->position = $position;

        return $this;
    }

    public function getQuiz(): ?Quiz {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static {
        if(!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static {
        if($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    public function getChoices(): ?bool {
        return $this->choices;
    }

    public function setChoices(bool $choices): static {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @return Collection<int, QuestionMetadata>
     */
    public function getMetadatas(): Collection {
        return $this->metadatas;
    }

    public function addMetadata(QuestionMetadata $metadata): static {
        if(!$this->metadatas->contains($metadata)) {
            $this->metadatas->add($metadata);
            $metadata->setQuestion($this);
        }

        return $this;
    }


    public function removeMetadata(QuestionMetadata $metadata): static {
        if($this->metadatas->removeElement($metadata)) {
            // set the owning side to null (unless already changed)
            if($metadata->getQuestion() === $this) {
                $metadata->setQuestion(null);
            }
        }

        return $this;
    }

    public function getArc(): ?Arc
    {
        return $this->arc;
    }

    public function setArc(?Arc $arc): static
    {
        $this->arc = $arc;

        return $this;
    }
}

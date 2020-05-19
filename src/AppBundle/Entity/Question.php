<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     * @Assert\Length(min=10)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var Adventure
     *
     * @ORM\ManyToOne(targetEntity="Adventure", inversedBy="questions")
     * @Assert\NotBlank()
     */
    private $adventure;

    /**
     * @var Answer[]|Collection
     *
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question", orphanRemoval=true, fetch="EAGER")
     */
    private $answers;

    /**
     * @var string
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(type="string", nullable=true)
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Blameable(on="change", field={"content"})
     */
    private $updatedBy;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    public function setContent(string $content): Question
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Adventure|null
     */
    public function getAdventure()
    {
        return $this->adventure;
    }

    public function setAdventure(Adventure $adventure): Question
    {
        $this->adventure = $adventure;

        return $this;
    }

    /**
     * @return Answer[]|Colllection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): string
    {
        return $this->updatedBy;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}

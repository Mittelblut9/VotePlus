<?php

namespace WbmVotePlus\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Article\Vote;
use Shopware\Models\Attribute\Customer;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_vote_plus")
 */
class VotePlus extends ModelEntity
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Article\Vote")
     * @ORM\JoinColumn(name="vote_id", referencedColumnName="articleID")
     */
    private Vote $vote;

    /** @ORM\Column(name="vote_id", type="integer") */
    private int $voteId;


    /** @ORM\Column(name="customer_id", type="integer") */
    private int $customerId;


    /** @ORM\Column(name="up", type="boolean") */
    private bool $up;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Vote
     */
    public function getVote(): Vote
    {
        return $this->vote;
    }

    /**
     * @param Vote $vote
     */
    public function setVote(Vote $vote): void
    {
        $this->vote = $vote;
    }

    /**
     * @return int
     */
    public function getVoteId(): int
    {
        return $this->voteId;
    }

    /**
     * @param int $voteId
     */
    public function setVoteId(int $voteId): void
    {
        $this->voteId = $voteId;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return bool
     */
    public function isUp(): bool
    {
        return $this->up;
    }

    /**
     * @param bool $up
     */
    public function setUp(bool $up): void
    {
        $this->up = $up;
    }



}
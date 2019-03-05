<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromoCodeRepository")
 */
class PromoCode
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
	private $code;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\TicketPlan")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $ticketPlan = null;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $activatedBy;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isActivated;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $bonusAmount = 0;

	public function getId()
	{
		return $this->id;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(string $code): self
	{
		$this->code = $code;

		return $this;
	}

	public function getTicketPlan(): ?TicketPlan
	{
		return $this->ticketPlan;
	}

	public function setTicketPlan(?TicketPlan $ticketPlan): self
	{
		$this->ticketPlan = $ticketPlan;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActivated(): bool
	{
		return $this->isActivated ?? false;
	}

	/**
	 * @param bool $isActivated
	 *
	 * @return PromoCode
	 */
	public function setIsActivated(bool $isActivated): self
	{
		$this->isActivated = $isActivated;

		return $this;
	}

	/**
	 * @param User $activatedBy
	 *
	 * @return PromoCode
	 */
	public function setActivatedBy(User $activatedBy): self
	{
		$this->activatedBy = $activatedBy;

		return $this;
	}

	/**
	 * @return User|null
	 */
	public function getActivatedBy(): ?User
	{
		return $this->activatedBy;
	}

	/**
	 * @param int $bonusAmount
	 *
	 * @return PromoCode
	 */
	public function setBonusAmount(int $bonusAmount): self
	{
		$this->bonusAmount = $bonusAmount;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getBonusAmount(): int
	{
		return $this->bonusAmount;
	}
}

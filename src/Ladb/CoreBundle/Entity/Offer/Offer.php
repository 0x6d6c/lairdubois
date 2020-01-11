<?php

namespace Ladb\CoreBundle\Entity\Offer;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Ladb\CoreBundle\Validator\Constraints as LadbAssert;
use Ladb\CoreBundle\Model\LocalisableExtendedInterface;
use Ladb\CoreBundle\Model\LocalisableExtendedTrait;
use Ladb\CoreBundle\Model\LocalisableInterface;
use Ladb\CoreBundle\Model\LocalisableTrait;
use Ladb\CoreBundle\Model\MultiPicturedInterface;
use Ladb\CoreBundle\Model\MultiPicturedTrait;
use Ladb\CoreBundle\Model\SluggedInterface;
use Ladb\CoreBundle\Model\SluggedTrait;
use Ladb\CoreBundle\Entity\AbstractDraftableAuthoredPublication;
use Ladb\CoreBundle\Model\BlockBodiedInterface;
use Ladb\CoreBundle\Model\BlockBodiedTrait;
use Ladb\CoreBundle\Model\CommentableTrait;
use Ladb\CoreBundle\Model\IndexableTrait;
use Ladb\CoreBundle\Model\LikableTrait;
use Ladb\CoreBundle\Model\PicturedTrait;
use Ladb\CoreBundle\Model\ScrapableTrait;
use Ladb\CoreBundle\Model\SitemapableInterface;
use Ladb\CoreBundle\Model\SitemapableTrait;
use Ladb\CoreBundle\Model\TaggableTrait;
use Ladb\CoreBundle\Model\TitledTrait;
use Ladb\CoreBundle\Model\ViewableTrait;
use Ladb\CoreBundle\Model\WatchableTrait;
use Ladb\CoreBundle\Model\JoinableInterface;
use Ladb\CoreBundle\Model\ScrapableInterface;
use Ladb\CoreBundle\Model\IndexableInterface;
use Ladb\CoreBundle\Model\TitledInterface;
use Ladb\CoreBundle\Model\PicturedInterface;
use Ladb\CoreBundle\Model\ViewableInterface;
use Ladb\CoreBundle\Model\LikableInterface;
use Ladb\CoreBundle\Model\WatchableInterface;
use Ladb\CoreBundle\Model\CommentableInterface;
use Ladb\CoreBundle\Model\ReportableInterface;
use Ladb\CoreBundle\Model\TaggableInterface;
use Ladb\CoreBundle\Model\ExplorableInterface;
use Ladb\CoreBundle\Entity\Find\Content\Event;

/**
 * @ORM\Table("tbl_offer")
 * @ORM\Entity(repositoryClass="Ladb\CoreBundle\Repository\Offer\OfferRepository")
 * @LadbAssert\BodyBlocks()
 */
class Offer extends AbstractDraftableAuthoredPublication implements TitledInterface, SluggedInterface, PicturedInterface, MultiPicturedInterface, BlockBodiedInterface, IndexableInterface, SitemapableInterface, TaggableInterface, ViewableInterface, ScrapableInterface, LikableInterface, WatchableInterface, CommentableInterface, ReportableInterface, ExplorableInterface, LocalisableInterface, LocalisableExtendedInterface {

	use TitledTrait, SluggedTrait, PicturedTrait, MultiPicturedTrait, BlockBodiedTrait;
	use IndexableTrait, SitemapableTrait, TaggableTrait, ViewableTrait, ScrapableTrait, LikableTrait, WatchableTrait, CommentableTrait, LocalisableTrait, LocalisableExtendedTrait;

	const CLASS_NAME = 'LadbCoreBundle:Offer\Offer';
	const TYPE = 122;

	const KIND_NONE = 0;
	const KIND_OFFER = 1;
	const KIND_REQUEST = 2;

	const CATEGORY_NONE = 0;
	const CATEGORY_JOB = 1;
	const CATEGORY_TOOL = 2;
	const CATEGORY_MATERIAL = 3;
	const CATEGORY_SERVICE = 4;
	const CATEGORY_OTHER = 99;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank()
	 * @Assert\Length(min=4)
	 */
	private $title;

	/**
	 * @Gedmo\Slug(fields={"title"}, separator="-")
	 * @ORM\Column(type="string", length=100, unique=true)
	 */
	private $slug;

	/**
	 * @ORM\Column(type="text", nullable=false)
	 */
	private $body;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $bodyExtract;

	/**
	 * @ORM\ManyToMany(targetEntity="Ladb\CoreBundle\Entity\Core\Block\AbstractBlock", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="tbl_offer_body_block", inverseJoinColumns={@ORM\JoinColumn(name="block_id", referencedColumnName="id", unique=true, onDelete="cascade")})
	 * @ORM\OrderBy({"sortIndex" = "ASC"})
	 * @Assert\Count(min=1)
	 */
	private $bodyBlocks;

	/**
	 * @ORM\Column(type="integer", name="body_block_picture_count")
	 */
	private $bodyBlockPictureCount = 0;

	/**
	 * @ORM\Column(type="integer", name="body_block_video_count")
	 */
	private $bodyBlockVideoCount = 0;

	/**
	 * @ORM\ManyToOne(targetEntity="Ladb\CoreBundle\Entity\Core\Picture", cascade={"persist"})
	 * @ORM\JoinColumn(nullable=true, name="main_picture_id")
	 */
	private $mainPicture;

	/**
	 * @ORM\ManyToMany(targetEntity="Ladb\CoreBundle\Entity\Core\Picture", cascade={"persist"})
	 * @ORM\JoinTable(name="tbl_offer_picture")
	 * @ORM\OrderBy({"sortIndex" = "ASC"})
	 * @Assert\Count(min=1, max=5)
	 */
	protected $pictures;

	/**
	 * @ORM\Column(type="smallint")
	 * @Assert\GreaterThan(value=0, message="Vous devez définir un type.")
	 */
	private $kind = self::KIND_NONE;

	/**
	 * @ORM\Column(type="smallint")
	 * @Assert\GreaterThan(value=0, message="Vous devez définir une catégorie.")
	 */
	private $category = self::CATEGORY_NONE;

	/**
	 * @ORM\Column(type="string", length=20)
	 */
	protected $price;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $rawPrice = 0;

	/**
	 * @ORM\Column(type="string", length=3)
	 */
	protected $currency = 'EUR';

	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $location;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 */
	private $latitude;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 */
	private $longitude;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $postalCode;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $locality;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $country;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $geographicalAreas;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $formattedAdress;

	/**
	 * @ORM\ManyToMany(targetEntity="Ladb\CoreBundle\Entity\Core\Tag", cascade={"persist"})
	 * @ORM\JoinTable(name="tbl_offer_tag")
	 * @Assert\Count(min=2)
	 */
	private $tags;

	/**
	 * @ORM\Column(type="integer", name="like_count")
	 */
	private $likeCount = 0;

	/**
	 * @ORM\Column(type="integer", name="watch_count")
	 */
	private $watchCount = 0;

	/**
	 * @ORM\Column(type="integer", name="comment_count")
	 */
	private $commentCount = 0;

	/**
	 * @ORM\Column(type="integer", name="view_count")
	 */
	private $viewCount = 0;

	/////

	public function __construct() {
		$this->bodyBlocks = new \Doctrine\Common\Collections\ArrayCollection();
		$this->pictures = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/////

	// NotificationStrategy /////

	public function getNotificationStrategy() {
		return self::NOTIFICATION_STRATEGY_FOLLOWER;
	}

	// Type /////

	public function getType() {
		return Offer::TYPE;
	}

	// Pictures /////

	public function getMaxPictureCount() {
		return 5;
	}

	// Kind /////

	public function getKind() {
		return $this->kind;
	}

	public function setKind($kind) {
		$this->kind = $kind;
		return $this;
	}

	// Category /////

	public function getCategory() {
		return $this->category;
	}

	public function setCategory($category) {
		$this->category = $category;
		return $this;
	}

	// Price /////

	public function getPrice() {
		return $this->price;
	}

	public function setPrice($price) {
		$this->price = $price;
		return $this;
	}

	// RawPrice /////

	public function getRawPrice() {
		return $this->rawPrice;
	}

	public function setRawPrice($rawPrice) {
		$this->rawPrice = $rawPrice;
		return $this;
	}

	// Currency /////

	public function getCurrency() {
		return $this->currency;
	}

	public function setCurrency($currency) {
		$this->currency = $currency;
		return $this;
	}

}

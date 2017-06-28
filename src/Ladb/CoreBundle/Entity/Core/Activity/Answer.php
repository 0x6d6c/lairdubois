<?php

namespace Ladb\CoreBundle\Entity\Core\Activity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("tbl_core_activity_answer")
 * @ORM\Entity(repositoryClass="Ladb\CoreBundle\Repository\Core\Activity\AnswerRepository")
 */
class Answer extends AbstractActivity {

	const CLASS_NAME = 'LadbCoreBundle:Core\Activity\Answer';
	const STRIPPED_NAME = 'anwser';

	/**
	 * @ORM\ManyToOne(targetEntity="Ladb\CoreBundle\Entity\Qa\Answer")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $answer;

	/////

	// StrippedName /////

	public function getStrippedName() {
		return self::STRIPPED_NAME;
	}

	// Answer /////

	public function getAnswer() {
		return $this->answer;
	}

	public function setAnswer(\Ladb\CoreBundle\Entity\Core\Answer $answer) {
		$this->answer = $answer;
		return $this;
	}

}
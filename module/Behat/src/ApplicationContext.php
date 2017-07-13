<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;

use Behat\Behat\Context\Context;
use Jobs\Entity\Job;
use Jobs\Repository\Job as JobRepository;

/**
 * Class ApplicationContext
 *
 * @package Yawik\Behat
 *
 * @author Anthonius Munthi <me@itstoni.com>
 */
class ApplicationContext implements Context
{
	use CommonContextTrait;
	
	/**
	 * @Given I apply for :title job
	 *
	 * @param string $title
	 * @throws \Exception when the titled job not exists
	 */
	public function iApplyAJob($title)
	{
		/* @var $repo JobRepository */
		$repo = $this->getRepository('Jobs/Job');
		$job = $repo->findOneBy(['title' => $title]);
		if(!$job instanceof Job){
			throw new \Exception('There is no job titled: "'.$title.'"');
		}
		$job->setApplyId($job->getId());
		$repo->store($job);
		
		$url = '/en/apply/'.$job->getApplyId();
		$this->visit($url);
	}
}
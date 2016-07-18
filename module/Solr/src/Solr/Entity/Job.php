<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Entity;

use Jobs\Entity\Job as BaseJob;

/**
 * Class Job
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Entity
 *
 * @ODM\Document(collection="jobs", repositoryClass="Solr\Repository\Job")
 */
class Job extends BaseJob
{

}
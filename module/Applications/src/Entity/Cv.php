<?php

namespace Applications\Entity;

use Cv\Entity\Cv as BaseCv;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Holds CV data of the application. CV data consists of the education history, work experiences and skills.
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 *
 * @ODM\EmbeddedDocument
 */
class Cv extends BaseCv
{
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */


namespace Cv\Entity;

use Core\Entity\IdentifiableEntityInterface;

interface EducationInterface extends IdentifiableEntityInterface
{
    
    public function setStartDate($startDate);
    public function getStartDate();
    public function setEndDate($endDate);
    public function getEndDate();
    public function setCurrentIndicator($currentIndicator);
    public function getCurrectIndicator();
    public function setCompetencyName($competencyName);
    public function getCompetencyName();
    public function setDescription($value);
    public function getDescription();
}

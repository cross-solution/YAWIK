<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Jobs\Form;

use Core\Form\HeadscriptProviderInterface;
use Doctrine\MongoDB\Cursor;
use Core\Form\Element\Select;

/**
 * Select element to select an organization.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.23
 */
class OrganizationSelect extends Select implements HeadscriptProviderInterface
{
    protected $scripts = ['modules/Jobs/js/form.organization-select.js'];

    public function setHeadscripts(array $scripts)
    {
        $this->scripts = $scripts;

        return $this;
    }

    public function getHeadscripts()
    {
        return $this->scripts;
    }

    public function init()
    {
        $this->setAttributes(['data-autoinit' => 'false', 'data-element' => 'organization-select']);
    }


    /**
     * Sets the selectable organizations.
     *
     * @param Cursor|array $organizations
     * @param bool         $addEmptyOption If true, an empty option is created as the first value option.
     *
     * @return self
     */
    public function setSelectableOrganizations($organizations, $addEmptyOption = true)
    {
        $options = $addEmptyOption ? ['0' => ''] : [];

        foreach ($organizations as $org) {
            /* @var $org \Organizations\Entity\Organization */

            $name     = $org->getOrganizationName()->getName();
            $contact  = $org->getContact();
            $image    = $org->getImage();
            $imageUrl = $image ? $image->getUri() : '';

            $options[$org->getId()] = $name . '|'
                                      . $contact->getCity() . '|'
                                      . $contact->getStreet() . '|'
                                      . $contact->getHouseNumber() . '|'
                                      . $imageUrl;
        }

        return $this->setValueOptions($options);
    }
}

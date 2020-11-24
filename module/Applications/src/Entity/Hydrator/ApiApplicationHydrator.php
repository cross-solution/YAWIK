<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright 2020 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

declare(strict_types=1);

namespace Applications\Entity\Hydrator;

use Applications\Entity\Attachment;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ApiApplicationHydrator extends ClassMethodsHydrator
{
    private $serverUrl;

    public function setServerUrl(string $serverUrl)
    {
        $this->serverUrl = $serverUrl;
    }

    public function hydrate(array $data, $object)
    {
        $applicationData = [];

        foreach ($data as $key => $value) {
            switch ($key) {
                default:
                    $applicationData[$key] = $value;
                    break;

                case "contact":
                    $contact = $object->getContact();
                    if (isset($value['birthday'])) {
                        [$year, $month, $day] = explode('-', $value['birthday']);
                        $value['birth_year'] = $year;
                        $value['birth_month'] = $month;
                        $value['birth_day'] = $day;
                    }
                    $hydrator = new ClassMethods();
                    $hydrator->addStrategy('image', new FileUploadStrategy(new Attachment()));
                    $hydrator->hydrate($value, $contact);
                    break;

                case "attachments":
                    $collection = $object->getAttachments();
                    $strategy = new FileUploadStrategy(new Attachment());
                    foreach ($value as $uploadedFileData) {
                        $file = $strategy->hydrate($uploadedFileData);
                        $collection->add($file);
                    }
                    break;

                case "facts":
                    $embedded = $object->{"get$key"}();
                    parent::hydrate($value, $embedded);
                    break;
            }
        }

        parent::hydrate($applicationData, $object);

        return $object;
    }

    public function extract($object): array
    {
        $data = parent::extract($object);
        $data['job'] = $object->getJob()->getApplyId();
        $data['user'] = $object->getUser() ? $object->getUser()->getId() : null;
        $data['contact'] = parent::extract($object->getContact());
        $data['contact']['image'] =
            $object->getContact()->getImage()
            ? $this->serverUrl . $object->getContact()->getImage()->getUri()
            : null
        ;
        $data['attachments'] = [];
        foreach ($object->getAttachments() as $file) {
           $data['attachments'][] = $this->serverUrl . $file->getUri();
        }

        unset(
            $data['is_draft'],
            $data['history'],
            $data['read_by'],
            $data['subscriber'],
            $data['permissions'],
            $data['refs'],
            $data['searchable_properties'],
            $data['keywords'],
            $data['comments'],
            $data['comments_message'],
            $data['rating'],
            $data['attributes'],
            $data['cv']
        );
        return $data;
    }
}

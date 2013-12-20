<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileRepository.php */ 
namespace Auth\Repository;

use Core\Repository\FileRepository as CoreFileRepository;

class FileRepository extends CoreFileRepository
{
    public function saveUploadedFile(array $fileData)
    {
        $userId = $fileData['meta']['user'];
        $fileIds = $this->getMapper()->fetchIdsByUser($userId);
        foreach ($fileIds as $id) {
            $this->delete($id);
        }
        return parent::saveUploadedFile($fileData);
    }
} 


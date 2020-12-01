<?php

declare(strict_types=1);

namespace OrganizationsTest\Functional;


use Core\Service\FileManager;
use CoreTestUtils\TestCase\FunctionalTestCase;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationImageMetadata;
use Organizations\Entity\OrganizationName;

class OrganizationFunctionalTest extends FunctionalTestCase
{
    /**
     * Test adding image for organization
     */
    public function testCreateOrganization()
    {
        /* @var \Core\Service\FileManager $fileManager */
        $org = $this->getOrganization();
        $fileManager = $this->getService(FileManager::class);
        $metadata = new OrganizationImageMetadata();
        $metadata
            ->setBelongsTo($org->getId())
            ->setKey('original')
            ->setOrganization($org)
        ;
        /* @var OrganizationImage $file */
        $file = $fileManager->uploadFromFile(OrganizationImage::class, $metadata, __FILE__, 'test');
        $org->getImages()->add($file);
        $this->getDoctrine()->persist($org);
        $this->getDoctrine()->flush();

        $images = $org->getImages()->getImages();
        $this->assertEquals(1, count($images));
    }

    /**
     * @depends testCreateOrganization
     */
    public function testFoo()
    {
        $org = $this->getOrganization();
        $images = $org->getImages()->getImages();
        $this->assertCount(1, $images);
    }

    private function getOrganization(string $name = 'PHPUnit Test'): Organization
    {
        $repo = $this->getDoctrine()->getRepository(Organization::class);
        $org = $repo->findOneBy(['_organizationName' => $name]);

        if(is_null($org)){
            $org = new Organization();
            $org->setOrganizationName(new OrganizationName($name));
            $this->getDoctrine()->persist($org);
            $this->getDoctrine()->flush();
        }
        return $org;
    }
}
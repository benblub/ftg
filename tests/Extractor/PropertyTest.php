<?php

namespace Tests\Extractor;

use App\Benblub\FtgBundle\Extractor\Property;
use DateTime;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Url;

class PropertyTest extends KernelTestCase
{
    private Property $property;

    public function setUp(): void
    {
        self::bootKernel();
        $this->property = static::getContainer()->get('App\Benblub\FtgBundle\Extractor\Property');
    }

    /**
     * @TODO add an TestEntity in this bundle to decouple from Project and possibility to test all Cases
     */
    public function testGetProbsFromEntity()
    {
        $ref = new ReflectionClass('App\Entity\User');
        $probs = $this->property->getScalarPropertiesFromDoctrineFieldMappings($ref);

        $this->assertCount(6, $probs);
        $this->assertIsArray($probs['roles']);
        $this->assertEquals('ROLE_USER', $probs['roles'][0]);
        $this->assertTrue($probs['isVerifiedAccount']);
        $this->assertEquals(10, strlen($probs['lastLogin']));
    }

    public function testGenerateEmail()
    {
        $email = $this->property->createScalarProperties('string', [], 'email');

        $this->assertStringContainsString('@', $email);
    }

    public function testGenerateFirstname()
    {
        $firstName = $this->property->createScalarProperties('string', [], 'firstName');

        $this->assertIsString($firstName);
    }

    public function testGenerateInteger()
    {
        $int = $this->property->createScalarProperties('integer', [], 'postalCode');

        $this->assertIsInt($int);
    }

    public function testGenerateBool()
    {
        $bool = $this->property->createScalarProperties('boolean', [], 'isActive');

        $this->assertTrue($bool);
        $this->assertIsBool($bool);
    }

    public function testGenerateFloat()
    {
        $float = $this->property->createScalarProperties('float', [], 'price');

        $this->assertIsFloat($float);
    }

    public function testCreateJsonPropertyWithFieldNameRoles()
    {
        $roleUser = $this->property->createJsonPropertyFromAnnotationOrFieldName([], 'roles');

        $this->assertIsArray($roleUser);
        $this->assertEquals('ROLE_USER', $roleUser[0]);
    }

    public function testCreateJsonPropertyWithDefault()
    {
        $roleUser = $this->property->createJsonPropertyFromAnnotationOrFieldName([], 'foo');

        $this->assertIsArray($roleUser);
        $this->assertEquals('DEFAULT', $roleUser[0]);
    }

    public function testCreateStringPropertyFromAnnotationOrFieldNameWithFieldName()
    {
        $displayName = $this->property->createStringPropertyFromAnnotationOrFieldName([], 'displayName');

        $this->assertIsString($displayName);
    }

    public function testCreateStringPropertyFromAnnotationOrFieldNameWithAnnotationEmail()
    {
        $emailConstraint = new Email();
        $email = $this->property->createStringPropertyFromAnnotationOrFieldName([$emailConstraint], 'displayName');

        $this->assertStringContainsString('@', $email);
    }

    public function testCreateStringPropertyFromAnnotationOrFieldNameWithAnnotationUrl()
    {
        $urlConstraint = new Url();
        $url = $this->property->createStringPropertyFromAnnotationOrFieldName([$urlConstraint], 'displayName');

        $this->assertStringContainsString('http', $url);
    }

    public function testCreateDate()
    {
        // reset our properties
        $this->property->setProperties([]);

        $this->property->createDateTimeProperty('someNiceDateField');

        // assert
        $props = $this->property->getProperties();
        $date = new DateTime($props['someNiceDateField']);
        $this->assertCount(1, $props);
        $this->assertEquals(10, strlen($props['someNiceDateField']));
        $this->assertInstanceOf(DateTime::class, $date);
    }
}

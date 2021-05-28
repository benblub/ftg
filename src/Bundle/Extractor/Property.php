<?php

namespace Benblub\Ftg\Bundle\Extractor;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ReflectionClass;
use ReflectionProperty;
use function Zenstruck\Foundry\faker;

class Property
{
    private array $properties = [];
    private array $defaultProperties = [];
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * We only want create Defaults for non NULL probs
     * Dont create Default for field id
     * We only want defaults for simple scalar types
     * Based on fieldName or asserts we do a second level select on faker
     */
    public function getScalarPropertiesFromDoctrineFieldMappings(ReflectionClass $entity): array
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();

        $classMetaData = $this->em->getClassMetadata($entity->getName());
        $this->getDefaultFromProperty($entity);

        foreach ($classMetaData->fieldMappings as $property) {
            // CREATE SCARLAR
            if (!$property['nullable']
                && true === ScalarType::isScalarType($property['type'])
                && $property['fieldName'] !== 'id') {

                // Also we should check property name and as examble for things like company create faker()->company()
                // if there is an default property which is not null|''|false we can use it? $this->>defaultProperties
                $reflectionProperty = new ReflectionProperty($entity->getName(), $property['fieldName']);
                $propertyAnnotation = $reader->getPropertyAnnotations($reflectionProperty);
                $this->properties[$property['fieldName']] = $this->createScalarProperties($property['type'], $propertyAnnotation, $property['fieldName']);
            }

            // CREATE JSON
            if (!$property['nullable']
                && 'json' === $property['type']
                && $property['fieldName'] !== 'id') {

                $this->createJsonProperty($propertyAnnotation, $property['fieldName']);
            }

            // CREATE DATETIME
            if (!$property['nullable']
                && 'datetime' === $property['type']
                && $property['fieldName'] !== 'id') {

                $this->createDateTimeProperty($property['fieldName']);
            }
        }

        return $this->properties;
    }

    /**
     * For the simple scalar types we can use faker() to create our defaults
     * But there is something we need to investigate
     * An Email string as examble with an Assert/Email needs to faker()->email()
     * TODO map scalar types to faker methods
     *
     * @return mixed <int|string|float|bool>
     *
     */
    public function createScalarProperties(string $type, $propertyAnnotation, string $fieldName)
    {
        switch ($type) {
            case 'string';
                // check propertyAnnotation
                return $this->createStringPropertyFromAnnotationOrFieldName($propertyAnnotation, $fieldName);
            case 'int':
                return faker()->randomNumber();
            case 'boolean':
                return true; // is a bool always true???
            case 'float':
                return '2.5';

            default:
                throw new Exception('type not found: ' . $type);
        }
    }

    public function createJsonProperty($propertyAnnotation, string $fieldName)
    {
        $this->properties[$fieldName] = $this->createJsonPropertyFromAnnotationOrFieldName($propertyAnnotation, $fieldName);
    }

    public function createDateTimeProperty(string $fieldName)
    {
        $this->properties[$fieldName] =  faker()->dateTime()->format('Y-m-d');
    }

    public function createDateProperty(string $fieldName)
    {
        $this->properties[$fieldName] =  faker()->date()->format('Y-m-d');
    }

    public function createStringPropertyFromAnnotationOrFieldName(array $propertyAnnotations, $fieldName): string
    {
        foreach ($propertyAnnotations as $key => $constraint) {
            $classname = get_class($constraint);
            // Now we can look for keys contains to generate different string with faker..
            // Email, Url, Hostname, Uuid,
                switch ($classname) {
                    case 'Symfony\Component\Validator\Constraints\Email';
                        return faker()->email();
                    case 'Symfony\Component\Validator\Constraints\Url';
                        return faker()->url();
                }
        }

        // try to Generate string from fieldname
        // roles, company, email, password, url, firstName, name|lastName, title, address, city, country, isbn, ....
        switch ($fieldName) {
            case 'company':
                return faker()->company();
        }

        return faker()->sentence();
    }

    /**
     * if we found some fieldName we know then we create it with faker
     *
     * @return string[]
     */
    public function createJsonPropertyFromAnnotationOrFieldName(array $propertyAnnotations, $fieldName): array
    {
        switch ($fieldName) {
            case 'roles':
                return ['ROLE_USER'];
        }

        return ['foo'];
    }

    /**
     * We store defaults values from properties.
     * if there is an default property which is not null|''|false we can use it? $this->defaultProperties
     */
    public function getDefaultFromProperty(ReflectionClass $reflectionClass): void
    {
        $this->defaultProperties = $reflectionClass->getDefaultProperties();
    }

    /**
     * get relations which cant be NULL
     */
    public function getPropertiesFromDoctrineRelations()
    {

    }

    /**
     * If Factory exist create Property with Factory
     * A Factory should have all nesseccary fields because of this Extractor :yeah
     */
    public function createPropertyDefaultWithFactory()
    {

    }
}

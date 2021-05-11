<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use App\Test\CustomApiTestCase;
use App\Entity\<?= $entityShorName ?>;
use App\Tests\Factory\<?= $entityShorName ?>Factory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Proxy;


final class <?= $class_name ?> extends CustomApiTestCase
{
    use factories;

    const ENDPOINT = '/<?= $entityShorNameLowercase ?>';

    /**
     * @var <?= $entityShorName ?>|Proxy
     */
    private Proxy $entity;

    public function setUp(): void
    {
        parent::setUp();

        // This sets you a entity Objects which have the defaults from your Factory
        // Set all your require fields in defaults
        $this->entity = <?= $entityShorName ?>Factory::createOne();
    }

    public function testDenyCreateResource()
    {
        $this->client->request('POST', self::ENDPOINT, [
            'json' => UserFactory::myDefaults(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testDenyReadResource()
    {
        $this->client->request('GET', self::ENDPOINT . '/' . $this->entity ->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testDenyUpdateResource()
    {
        $this->client->request('PUT', self::ENDPOINT.'/'.$this->entity->getId(), [
            'json' => [
            // @todo add prob to update here
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testDenyDeleteResource()
    {
        $this->client->request('DELETE', self::ENDPOINT.'/'.$this->entity->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}

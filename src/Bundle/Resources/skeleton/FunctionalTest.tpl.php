<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use App\Test\CustomApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use App\Tests\Factory\<?= $entityShorName ?>Factory;
use Zenstruck\Foundry\Proxy;
use App\Entity\<?= $entityShorName ?>;


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

        // We set a Bearer Token to all our requests in this class from the created User
        $user = UserFactory::createOne(['roles' => ['<?= $role ?>']]);
        $this->setAuthenticationHeader($user->getId());

        // This sets you a entity Objects which have the defaults from your Factory
        // Set all your require fields in defaults
        $this->entity = <?= $entityShorName ?>Factory::createOne();
    }

    public function testCreateResource()
    {
        $this->client->request('POST', self::ENDPOINT, [
            'json' => UserFactory::myDefaults(),
        ]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        // @todo add snapshots code here ;) ?
    }

    public function testReadResourceCollection()
    {
        $this->client->request('GET', self::ENDPOINT);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUpdateResource()
    {
        $this->client->request('PUT', self::ENDPOINT.'/'.$this->entity->getId(), [
            'json' => [
            // @todo add prob to update here
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testDeleteResource()
    {
        $this->client->request('DELETE', self::ENDPOINT.'/'.$this->entity->getId());

        $this->assertResponseStatusCodeSame(204);
    }
}

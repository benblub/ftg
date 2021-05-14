<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use App\Entity\<?= $entityShorName ?>;
use Benblub\Ftg\Bundle\Helper\AuthHelper;
use App\Tests\Factory\<?= $entityShorName ?>Factory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;


final class <?= $class_name ?> extends AuthHelper
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
        // for Entity User delete next line and move setAuthenticationHeader to Line 36 and replace $user->getId()
        // with $this->entity->getId()
        $user = UserFactory::createOne(['roles' => ['<?= $role ?>']]);
        $this->setIdentifier(['id' => $user->getId()]); // TODO this should come from config!
        $this->setAuthenticationHeader();

        // This sets you a entity Objects which have the defaults from your Factory
        // Set all your require fields in defaults
        $this->entity = <?= $entityShorName ?>Factory::createOne();
    }

    public function testCreateResource()
    {
        $this->client->request('POST', self::ENDPOINT, [
            'json' => UserFactory::myDefaults(),
        ]);


        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        // @todo add snapshots code here ;) ?
    }

    public function testReadResource()
    {
        $this->client->request('GET', self::ENDPOINT . '/' . $this->entity ->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUpdateResource()
    {
        $this->client->request('PUT', self::ENDPOINT.'/'.$this->entity->getId(), [
            'json' => [
            // @todo add prob to update here
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testDeleteResource()
    {
        $this->client->request('DELETE', self::ENDPOINT.'/'.$this->entity->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testReadResourceCollection()
    {
        // TODO we need to create some Resources and assert (we have 1 Resource allready in setup)

        $this->client->request('GET', self::ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}

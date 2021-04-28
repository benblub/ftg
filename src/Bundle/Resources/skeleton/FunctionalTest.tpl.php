<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $entity->getName() ?>;
use App\Test\CustomApiTestCase;

final class <?= $class_name ?> extends CustomApiTestCase
{
    const ENDPOINT = '<?= $entityShorNameLowercase ?>';

    private <?= $entityShorName ?> $entity;

    public function setUp(): void
    {
        // @todo add your setup here
        $this->entity = new <?= $entityShorName ?>(); // @todo create some UserObject here.. eg UserFactory::createOne as examble
    }

    public function testCreateResource()
    {
        $this->client->request('POST', self::ENDPOINT);

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

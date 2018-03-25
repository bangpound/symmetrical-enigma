<?php

namespace App\Controller;

use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation as Sensio;

/**
 * Class ProxyConsulController
 *
 * @package App\Controller
 * @Sensio\Route("/consul")
 */
class ProxyConsulController
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * ProxyConsulController constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Sensio\Route("/{path}", requirements={"path"=".+"})
     *
     * @param Request $request
     * @param string  $path
     *
     * @return Response
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(Request $request, string $path)
    {
        $response = $this->client->request(
            $request->getMethod(),
            $path,
            [
                'query' => $request->query->all()
            ]
        );

        return new Response(
            $response->getBody()->getContents(),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
}

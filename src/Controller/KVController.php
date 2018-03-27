<?php

namespace App\Controller;

use App\Form\KeyValueType;
use App\Model\KeyValue;
use SensioLabs\Consul\ConsulResponse;
use SensioLabs\Consul\Exception\ClientException;
use SensioLabs\Consul\Services\KVInterface;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

/**
 * Class KeyValueController
 *
 * @package App\Controller
 *
 * @Sensio\Route("/kv")
 */
class KVController
{
    /**
     * @var KVInterface
     */
    private $kv;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * KeyValueController constructor.
     *
     * @param KVInterface          $kv
     * @param FormFactoryInterface $formFactory
     * @param SerializerInterface  $serializer
     * @param RouterInterface      $router
     * @param Environment          $twig
     */
    public function __construct(
        KVInterface $kv,
        FormFactoryInterface $formFactory,
        SerializerInterface $serializer,
        RouterInterface $router,
        Environment $twig
    ) {
        $this->kv = $kv;
        $this->formFactory = $formFactory;
        $this->serializer = $serializer;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @Sensio\Route("/", name="kv_list_top")
     * @Sensio\Route("/{key}", name="kv_list", defaults={"key"=""}, requirements={"key"=".+"})
     * @Sensio\Method({"GET", "POST", "PUT", "DELETE"})
     *
     * @param Request $request
     * @param string|null  $key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \OutOfBoundsException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \InvalidArgumentException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     */
    public function __invoke(Request $request, ?string $key = null)
    {
        // @todo extract function that produces the keyvalue model.
        if ($key !== null) {
            $keyValue = new KeyValue($key);
        }

        // @todo extract function that produces the prefix model.
        $prefix = $key;
        if (null !== $key && !$keyValue->isFolder()) {
            $prefix = $keyValue->getParentKey();

            try {
                $response = $this->kv->get($key);
            } catch (ClientException $e) {
                throw new HttpException($e->getCode(), $e->getMessage());
            }

            \assert($response instanceof ConsulResponse);
            $keyValues = $this->serializer->deserialize(
                $response->getBody(),
                KeyValue::class.'[]',
                JsonEncoder::FORMAT
            );
            \assert(\is_array($keyValues));
            $keyValues = \array_map(function (KeyValue $keyValue) {
                $keyValue->setValue(base64_decode($keyValue->getValue()));
                return $keyValue;
            }, $keyValues);
            $keyValue = $keyValues[0];
        }

        // @todo extract function that returns keys for the given path.
        $response = $this->kv->get($prefix, [
            'keys' => true,
            'separator' => $request->query->get('separator', '/'),
        ]);
        \assert($response instanceof ConsulResponse);
        $keys = \array_map(function ($key) {
            return new KeyValue($key);
        }, $response->json());
        $keys = \array_filter($keys, function (KeyValue $key) use ($prefix) {
            return $key->getKey() !== $prefix;
        });

        $form = $this->createForm($keyValue ?? null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('kv')->getData();
            $keyValue = new KeyValue($data['key']);
            if ($data['value'] !== null) {
                $keyValue->setValue($data['value']);
            }

            \assert($form instanceof Form);
            $button = $form->getClickedButton();
            \assert($button instanceof Button);

            if ($button->getName() === 'save') {
                $response = $this->kv->put($keyValue->getKey(), $keyValue->getValue());
                return new RedirectResponse($this->router->generate('kv_list', ['key' => $keyValue->getKey()]));
            }

            if ($button->getName() === 'delete') {
                $response = $this->kv->delete($keyValue->getKey());
                return new RedirectResponse($this->router->generate('kv_list', ['key' => $prefix]));
            }
        }

        return new Response($this->twig->render('kv/index.html.twig', [
            'keys' => $keys,
            'key' => $keyValue ?? null,
            'form' => $form->createView(),
        ]));
    }

    /**
     * @param KeyValue|null $keyValue
     *
     * @return FormInterface
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createForm(?KeyValue $keyValue = null): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder(FormType::class);
        if ($keyValue) {
            $formBuilder->setData([
                'kv' => [
                    'key' => $keyValue->getKey(),
                    'value' => $keyValue->getValue()
                ],
            ]);
        }
        $formBuilder->add('kv', KeyValueType::class);
        $formBuilder->add('save', SubmitType::class, [
            'attr' => ['class' => 'btn-primary'],
        ]);
        $formBuilder->add('delete', SubmitType::class);

        return $formBuilder->getForm();
    }
}

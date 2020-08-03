<?php


namespace App\Controller\Main;

use App\Controller\Main\BaseController;
use App\Entity\Category;
use App\Entity\Expence;
use App\Form\CategoryType;
use App\Form\ExpenceType;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;

class ExpenceController extends BaseController
{
    /**
     * @Route("/expences", name="expence_list")
     */
    public function index()
    {
        $expence = $this->getDoctrine()->getRepository(Expence::class)
            ->findAll();

        $forRender = parent::renderDefault();
        $forRender['title'] = 'Расходы';
        $forRender['expence'] = $expence;
        return $this->render('main/expence/index.html.twig', $forRender);
    }

    /**
     * @Route("/expences/create", name = "expence_create")
     * @param Request $request
     */

    public function create(Request $request)
    {
        $expence = new Expence();
        $form = $this->createForm(ExpenceType::class, $expence);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $expence->setCreatedAtValue();
            #$user = $this->getUser();
            #$user = $this->get('security.token_storage')->getToken()->getUser();
            $user = $this->getUser();
            $expence->setUser($user);
            $em->persist($expence);
            $em->flush();
            $this->addFlash('success', 'Добавлен расход');
            return $this->redirectToRoute('expence_list');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Добавление расхода';
        $forRender['form'] = $form->createView();
        return $this->render('main/expence/form.html.twig', $forRender);
    }
    /**
     * @Route("expences/update/{id}", name = "expence_update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function update(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $expence = $this->getDoctrine()->getRepository(Expence::class)
            ->find($id);
        $form = $this->createForm(ExpenceType::class, $expence);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $this->addFlash('success', 'Расход обновлен');
            }
            if ($form->get('delete')->isClicked()) {
                $em->remove($expence);
                $this->addFlash('success', 'Расход удален');
            }
            $em->flush();
            return $this->redirectToRoute('expence_list');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Редактирование расхода';
        $forRender['form'] = $form->createView();
        return $this->render('main/expence/form.html.twig', $forRender);
    }

    /**
     * @Route("expences/show", name = "show")
     */
    public function show()
    {
        // You only need to provide the path to your static file
        // $filepath = 'path/to/TextFile.txt';

        // i.e Sending a file from the resources folder in /web
        // in this example, the TextFile.txt needs to exist in the server
        $publicResourcesFolderPath = $this->getParameter('kernel.project_dir') . '/';
        $filename = "README.md";

        // This should return the file located in /mySymfonyProject/README.md
        // to being viewed in the Browser
        return new BinaryFileResponse($publicResourcesFolderPath.$filename);
    }

    /**
     * @Route("expences/download", name = "expence_download")
     */
    public function download()
    {
        #$publicResourcesFolderPath = $this->getParameter('kernel.project_dir') . '/';
        #$filename = "README.md";
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format(\DateTime::ISO8601) : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
            ],
        ];

        $dateTimeNormalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);

        $encoders = [new CsvEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $normalizers = [new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter), $dateTimeNormalizer];

        $serializer = new Serializer($normalizers, $encoders);
        $user = $this->getUser();
        $id = $user->getId();
        $filename = 'expences_'.$id.'.csv';
        $em = $this->getDoctrine()->getManager();
        // The dinamically created content of the file
        $fileContent = $this->getDoctrine()->getRepository(Expence::class)
        ->findBy(array('user' => $id));
        $fileContent = $serializer->normalize($fileContent, null, [AbstractNormalizer::ATTRIBUTES => ['created_at', 'title', 'category' => ['title'], 'amount']]);
        $fileContentString = $serializer->serialize($fileContent, 'csv');
        #$fileContentString = implode('\n', $fileContent);
        // Return a response with a specific content
        $response = new Response($fileContentString);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;
    }
}
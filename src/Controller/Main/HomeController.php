<?php


namespace App\Controller\Main;

use App\Entity\Category;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $forRender = parent::renderDefault();
        return $this->render('main/index.html.twig', $forRender);
    }

    /**
     * @Route("/user/create", name = "user_create")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            #$user->setRoles(["ROLE_ADMIN"]);
            $em->persist($user);
            $em->flush();
            $this->createDefault($user);
            return $this->redirectToRoute('home');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Форма регистрации пользователя';
        $forRender['form'] = $form->createView();


        return $this->render('main/regForm.html.twig', $forRender);

    }

    private function createDefault($user)
    {
        $em = $this->getDoctrine()->getManager();
        $category1 = new Category();
        $category1->setTitle('Продукты');
        $category2 = new Category();
        $category2->setTitle('Одежда');
        $category3 = new Category();
        $category3->setTitle('Транспорт');
        $category4 = new Category();
        $category4->setTitle('Досуг');
        $categories = [$category1, $category2, $category3, $category4];
        foreach ($categories as $category){
            $category->setUser($user);
            $em->persist($category);
            $em->flush();
        }

        return $categories;
    }

}
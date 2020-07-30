<?php


namespace App\Controller\Main;


use App\Controller\Main\BaseController;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends BaseController
{
    /**
     * @Route("/categories", name="category_list")
     */
    public function index()
    {
        $user = $this->getUser();
        $id = $user->getId();
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy(array('user' => $id));
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Управление категориями';
        $forRender['categories'] = $categories;
        return $this->render('main/category/index.html.twig', $forRender);
    }


    /**
     * @Route("/categories/create", name = "category_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */

    public function create(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            $category->setUser($user);
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Категория создана');
            return $this->redirectToRoute('category_list');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Создание категории';
        $forRender['form'] = $form->createView();
        return $this->render('main/category/form.html.twig', $forRender);
    }

    /**
     * @Route("categories/update/{id}", name = "category_update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function update(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Category::class)
            ->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $this->addFlash('success', 'Категория обновленна');
            }
            if ($form->get('delete')->isClicked()) {
                $em->remove($category);
                $this->addFlash('success', 'Категория удалена');
            }
            $em->flush();
            return $this->redirectToRoute('category_list');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Редактирование категории';
        $forRender['form'] = $form->createView();
        return $this->render('main/category/form.html.twig', $forRender);
    }



    }
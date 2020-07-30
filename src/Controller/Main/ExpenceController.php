<?php


namespace App\Controller\Main;

use App\Controller\Main\BaseController;
use App\Entity\Category;
use App\Entity\Expence;
use App\Form\CategoryType;
use App\Form\ExpenceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
}
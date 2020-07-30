<?php


namespace App\Controller\Main;



use App\Entity\Expence;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends BaseController
{
    /**
     * @Route("/expences/statistics", name="statistics")
     */
    public function index()
    {
        $expence = $this->getDoctrine()->getRepository(Expence::class)
            ->findAll();

        $forRender = parent::renderDefault();
        $forRender['title'] = 'Статистика расходов';
        $forRender['expence'] = $expence;
        return $this->render('main/expence/statistics/index.html.twig', $forRender);
    }
}
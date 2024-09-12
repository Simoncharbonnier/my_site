<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\QuestionRepository;

class QuestionController extends AbstractController
{
    /**
     * Question of the day page
     * @param QuestionRepository $questionRepository question repository
     */
    #[Route('/question', name: 'app_question')]
    public function index(QuestionRepository $questionRepository): Response
    {
        $question = $questionRepository->findOneBy(['day' => date('d'), 'month' => date('m')]);

        if (empty($question)) {
            $this->addFlash('danger', 'Il n\'y a pas de question aujourd\'hui, contacte Simon pour plus d\'infos !');
        }

        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
            'question' => $question
        ]);
    }
}

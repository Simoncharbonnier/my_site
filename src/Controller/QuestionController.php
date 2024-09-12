<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AnswerFormType;
use App\Entity\Answer;

class QuestionController extends AbstractController
{
    /**
     * Question of the day page
     * @param string $month month
     * @param string $day day
     * @param QuestionRepository $questionRepository question repository
     * @param Request $request request
     * @param EntityManagerInterface $entityManager entity manager
     */
    #[Route('/question/{month}/{day}', name: 'app_question')]
    public function question(
        string $month,
        string $day,
        QuestionRepository $questionRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $question = $questionRepository->findOneBy(['month' => $month, 'day' => $day]);

        $alreadyAnswered = false;
        foreach ($question->getAnswers() as $answer) {
            if ($answer->getUser() !== $this->getUser()) {
                $question->removeAnswer($answer);
            } else if ($answer->getYear() === intval(date('Y'))) {
                $alreadyAnswered = true;
            }
        }

        $form = $this->createForm(AnswerFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newAnswer = new Answer();
            $newAnswer->setContent($form->get('content')->getData());
            $newAnswer->setYear(intval(date('Y')));
            $newAnswer->setQuestion($question);
            $newAnswer->setUser($this->getUser());

            $entityManager->persist($newAnswer);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez répondu à la question du jour !');
            return $this->redirectToRoute('app_question', ['month' => $month, 'day' => $day]);
        }

        return $this->render('question/index.html.twig', [
            'today_question' => date('m') === $month && date('d') === $day,
            'day' => $day,
            'month' => $month,
            'question' => $question,
            'form' => $form,
            'already_answered' => $alreadyAnswered
        ]);
    }
}

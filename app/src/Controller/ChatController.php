<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Form\ChatType;
use App\Form\MessageType;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chat')]
final class ChatController extends AbstractController
{
    #[Route(name: 'app_chat_index', methods: ['GET'])]
    public function index(ChatRepository $chatRepository): Response
    {
        return $this->render('chat/index.html.twig', [
            'chats' => $chatRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chat = new Chat();
        $user = $this->getUser();

        $chat->setOwner($user);
        $entityManager->persist($chat);
        $entityManager->flush();

        return $this->redirectToRoute('app_chat_show', ['id' => $chat->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_chat_show', methods: ['GET'])]
    public function show(Request $request, Chat $chat): Response
    {
        $message = new Message();
        $messages = $chat->getMessages()->toArray();

        $form = $this->createForm(MessageType::class, $message, [
            'action' => $this->generateUrl('app_message_new', [
                'chatId' => $chat->getId()
            ])
        ]);

        return $this->render('chat/show.html.twig', [
            'chat' => $chat,
            'messages' => $messages,
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chat $chat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatType::class, $chat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat/edit.html.twig', [
            'chat' => $chat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_delete', methods: ['POST'])]
    public function delete(Request $request, Chat $chat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_index', [], Response::HTTP_SEE_OTHER);
    }
}

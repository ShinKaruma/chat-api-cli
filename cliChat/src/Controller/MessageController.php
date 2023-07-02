<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MessageType;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function PHPSTORM_META\type;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message', methods:['GET'])]
    public function index(HttpClientInterface $client): Response
    {
        $message = new Message();
        $messageForm = $this->createForm(MessageType::class, $message);

        $response = $client->request('get', 'http://127.0.0.1/api/messages');
        
        $messages = $response->toArray();

        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
            'messageForm' => $messageForm,
            'messages' => $messages
        ]);
    }

    #[Route('/message', name:'app_message_post', methods:['POST'])]
    public function messagePost(HttpClientInterface $client, Request $request) : Response {
        $messageForm = $this->createForm(MessageType::class);
        $messageForm->handleRequest($request);
        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            
            $response = $client->request('POST', 'http://127.0.0.1/api/message', [
                'body' => $messageForm->getData()->getArray()
            ]);
        }

        return $this->redirectToRoute('app_message');
    }
}

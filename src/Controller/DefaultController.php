<?php

namespace App\Controller;

use ActivityPub\Type\Extended\Object\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/add-entity", name="app_lucky_number")
     */
    public function addEntity()
    {
        $em = $this->getDoctrine()->getManager();

        $note = new Note();
        $note->setContent('Hello World');

        $em->persist($note);
        $em->flush();

        return new Response(
            '<html><body>Note created</body></html>'
        );
    }
}
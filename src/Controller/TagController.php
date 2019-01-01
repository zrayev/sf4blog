<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends AbstractController
{
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository(Tag::class)->findAll();
        $paginateTags = $paginator->paginate($tags, $request->query->getInt('page', 1), 10);

        return $this->render('tag/index.html.twig', [
            'tags' => $paginateTags,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $tag = new Tag();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your tag  with title - ' . $tag->getTitle() . ' were saved!'
            );

            return $this->redirectToRoute('tag_new');
        }

        return $this->render('tag/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Tag $tag
     * @ParamConverter("tag", class="App:Tag")
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('tags');
        }

        return $this->render('tag/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }
}

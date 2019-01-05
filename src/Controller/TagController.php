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
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $tag = new Tag();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash(
                'notice',
                $translator->trans('Your tag  with title') . ' - ' . $tag->getTitle() . $translator->trans('were saved') . '!'
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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

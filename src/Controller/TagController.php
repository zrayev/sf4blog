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
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class TagController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addRouteItem('Home', 'index');
        $breadcrumbs->addItem('Tags', $this->get('router')->generate('tags'));
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository(Tag::class)->findAllQuery();
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
    public function new(Request $request, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $breadcrumbs->addItem('Tags', $this->get('router')->generate('tags'));
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
                $this->translator->trans('notification.tag_created', [
                    '%title%' => $tag->getTitle(),
                ])
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
     * @param Breadcrumbs $breadcrumbs
     *
     * @return RedirectResponse|Response
     * @ParamConverter("tag", class="App:Tag")
     */
    public function edit(Request $request, Tag $tag, Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $breadcrumbs->addItem('Tags', $this->get('router')->generate('tags'));
        $breadcrumbs->addItem($tag->getTitle());
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash(
                'notice',
                $this->translator->trans('notification.tag_edited', [
                    '%title%' => $tag->getTitle(),
                ])
            );

            return $this->redirectToRoute('tags');
        }

        return $this->render('tag/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }
}
